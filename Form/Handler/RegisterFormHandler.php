<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Form\Handler;

use Black\Bundle\UserBundle\Doctrine\UserManager;
use Black\Bundle\UserBundle\Model\RegistrationInterface;
use Black\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Black\Bundle\UserBundle\Model\UserInterface;
use Black\Bundle\UserBundle\Mailer\Mailer;

/**
 * Class RegisterFormHandler
 *
 * @package Black\Bundle\UserBundle\Form\Handler
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class RegisterFormHandler
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var
     */
    protected $factory;

    /**
     * @var \Black\Bundle\UserBundle\Mailer\Mailer
     */
    protected $mailer;

    /**
     * @var \Black\Bundle\PageBundle\Model\PageManagerInterface
     */
    protected $userManager;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var
     */
    protected $url;

    /**
     * @param FormInterface           $form
     * @param Request                 $request
     * @param SessionInterface        $session
     * @param EncoderFactoryInterface $factory
     * @param Mailer                  $mailer
     */
    public function __construct(FormInterface $form, UserManagerInterface $userManager, Request $request, Router $router, SessionInterface $session, EncoderFactoryInterface $factory, Mailer $mailer)
    {
        $this->form         = $form;
        $this->userManager  = $userManager;
        $this->request      = $request;
        $this->router       = $router;
        $this->session      = $session;
        $this->factory      = $factory;
        $this->mailer       = $mailer;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function process(RegistrationInterface $register)
    {
        $this->form->setData($register);

        if ('POST' === $this->request->getMethod()) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                return $this->onSave($register->getUser());
            } else {
                return $this->onFailed();
            }
        }
    }

    /**
     * @param PageInterface $page
     *
     * @return mixed
     */
    protected function onSave(UserInterface $user)
    {
        $encoder = $this->getEncoder($user);
        $user->encodePassword($encoder);
        $user->addRole('ROLE_USER');
        $user->setConfirmationToken($token = $this->generateToken());

        $this->userManager->persistAndFlush($user);
        $this->mailer->sendRegisterMessage($user, $token);

        if ($this->form->get('save')->isClicked()) {
            $this->setUrl($this->generateUrl('register_success', array('username' => $user->getUsername())));

            return true;
        }
    }

    /**
     * @return bool
     */
    protected function onFailed()
    {
        $this->setFlash('error', 'error.user.www.register');

        return false;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }


    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    protected function generateToken()
    {
        $token = sha1(uniqid(mt_rand(), true));

        return $token;
    }

    /**
     * @param $object
     *
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    protected function getEncoder($object)
    {
        return $this->factory->getEncoder($object);
    }

    /**
     * @param $name
     * @param $msg
     *
     * @return mixed
     */
    protected function setFlash($name, $msg)
    {
        return $this->session->getFlashBag()->add($name, $msg);
    }

    /**
     * @param       $route
     * @param array $parameters
     * @param       $referenceType
     *
     * @return mixed
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }
}
