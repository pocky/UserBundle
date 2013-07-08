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

class FrontUserFormHandler
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
                $this->checkActivity($user);

                $this->setFlash('success', 'success.user.www.edit');

                return true;
            } else {
                $this->setFlash('error', 'error.user.www.edit');
            }
        }
    }

    public function getForm()
    {
        return $this->form;
    }

    protected function checkActivity(UserInterface $user)
    {
        if (false == $user->getIsActive()) {
            $user->setIsActive(false);
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
