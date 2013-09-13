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

/**
 * Class FrontUserFormHandler
 *
 * @package Black\Bundle\UserBundle\Form\Handler
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class FrontUserFormHandler
{
    protected $request;
    protected $form;
    protected $factory;
    protected $session;

    /**
     * @param FormInterface           $form
     * @param Request                 $request
     * @param SessionInterface        $session
     * @param EncoderFactoryInterface $factory
     */
    public function __construct(FormInterface $form, Request $request, SessionInterface $session, EncoderFactoryInterface $factory)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->session  = $session;
        $this->factory  = $factory;
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function process(UserInterface $user)
    {
        $this->form->setData($user);
        $user->setTermsAccepted(true);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {

                $encoder = $this->getEncoder($user);

                $user->encodePassword($encoder);
                $this->checkActivity($user);

                $this->setFlash('success', 'success.user.www.edit');

                return true;
            } else {
                $this->setFlash('error', 'error.user.www.edit');
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
}
