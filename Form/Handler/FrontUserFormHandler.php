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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Black\Bundle\UserBundle\Model\UserInterface;
use Black\Bundle\UserBundle\Doctrine\UserManager;
use Black\Bundle\UserBundle\Model\RegistrationInterface;
use Black\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FrontUserFormHandler
 *
 * @package Black\Bundle\UserBundle\Form\Handler
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class FrontUserFormHandler
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
    public function __construct(FormInterface $form, UserManagerInterface $userManager, Request $request, Router $router, SessionInterface $session, EncoderFactoryInterface $factory)
    {
        $this->form         = $form;
        $this->userManager  = $userManager;
        $this->request      = $request;
        $this->router       = $router;
        $this->session      = $session;
        $this->factory      = $factory;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function process(UserInterface $user)
    {
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {

                if ($this->form->isValid()) {
                    return $this->onSave($user);
                } else {
                    return $this->onFailed();
                }
            }
        }
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
     * @param PageInterface $page
     *
     * @return mixed
     */
    protected function onSave(UserInterface $user)
    {
        $encoder = $this->getEncoder($user);

        $user->encodePassword($encoder);
        $this->checkActivity($user);

        $this->userManager->flush();

        if ($this->form->get('save')->isClicked()) {
            $this->setFlash('success', 'www.user.profile.settings.success');
            $this->setUrl($this->generateUrl('user_settings'));

            return true;
        }
    }

    /**
     * @return bool
     */
    protected function onFailed()
    {
        $this->setFlash('error', 'error.user.www.edit');

        return false;
    }

    /**
     * @param UserInterface $user
     */
    protected function checkActivity(UserInterface $user)
    {
        if (false == $user->getIsActive()) {
            $user->setIsActive(false);
        }
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
