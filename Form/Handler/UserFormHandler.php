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

class UserFormHandler
{
    protected $request;
    protected $form;
    protected $factory;
    protected $session;

    public function __construct(
        FormInterface $form,
        Request $request,
        SessionInterface $session,
        EncoderFactoryInterface $factory
    ) {
        $this->form     = $form;
        $this->request  = $request;
        $this->session  = $session;
        $this->factory  = $factory;
    }

    public function process(UserInterface $user)
    {
        $this->form->setData($user);

        if ('POST' === $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $encoder = $this->getEncoder($user);
                $user->encodePassword($encoder);

                $this->addRoles($user);

                $this->setFlash('success', 'success.user.admin.edit');

                return true;
            } else {
                $this->setFlash('error', 'error.user.admin.edit');
            }
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    protected function addRoles(UserInterface $user)
    {
        if (!$user->hasRole('ROLE_USER')) {
            $user->addRole('ROLE_USER');
        }

        if ($user->getIsRoot() && !$user->hasRole('ROLE_SUPER_ADMIN')) {
            $user->addRole('ROLE_SUPER_ADMIN');
        } else {
            $user->removeRole('ROLE_SUPER_ADMIN');
        }
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
