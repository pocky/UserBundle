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
 * Class UserFormHandler
 *
 * @package Black\Bundle\UserBundle\Form\Handler
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 */
class UserFormHandler
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

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
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
    protected function addRoles(UserInterface $user)
    {
        if (!$user->hasRole('ROLE_USER')) {
            $user->addRole('ROLE_USER');
        }

        if ($user->getIsRoot()) {
            $user->addRole('ROLE_SUPER_ADMIN');
        } else {
            $user->removeRole('ROLE_SUPER_ADMIN');
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
