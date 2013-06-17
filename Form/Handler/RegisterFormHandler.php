<?php

/*
 * This file is part of the Blackengine package.
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
use Black\Bundle\UserBundle\Mailer\Mailer;

class RegisterFormHandler
{
    protected $request;
    protected $form;
    protected $factory;
    protected $session;

    public function __construct(FormInterface $form, Request $request, SessionInterface $session, EncoderFactoryInterface $factory, Mailer $mailer)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->session  = $session;
        $this->factory  = $factory;
        $this->mailer   = $mailer;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $encoder = $this->getEncoder($user);
                $user->encodePassword($encoder);
                $user->addRole('ROLE_USER');
                $user->setConfirmationToken($token = $this->generateToken());

                $this->mailer->sendRegisterMessage($user, $token);

                return true;
            } else {
                $this->setFlash('error', 'error.user.www.register');
            }
        }
    }

    public function generateToken()
    {
        $token = sha1(uniqid(mt_rand(), true));

        return $token;
    }

    public function getForm()
    {
        return $this->form;
    }

    protected function getEncoder($object)
    {
        return $this->factory->getEncoder($object);
    }

    protected function setFlash($name, $msg)
    {
        return $this->session->getFlashBag()->add($name, $msg);
    }
}