<?php

/*
 * This file is part of the Black package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Black\Bundle\UserBundle\Form\Type\RegisterType;
use Black\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class RegisterController
 *
 * @package Black\Bundle\UserBundle\Controller
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 * @Route("/register")
 */
class RegisterController extends Controller
{
    /**
     * @Route("/", name="_register_form")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        $formHandler    = $this->get('black_user.register.form.handler');

        return array(
            'form' => $formHandler->getForm()->createView()
        );
    }

    /**
     * @Route("/register", name="_register_process")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function registerAction()
    {
        $manager    = $this->getUserManager();
        $document   = $manager->createInstance();

        $formHandler    = $this->get('black_user.register.form.handler');
        $process        = $formHandler->process($document);

        if ($process) {
            $manager->persistAndFlush($document);

            return $this->redirect(
                $this->generateUrl('register_success', array('username' => $document->getUsername()))
            );
        }

        $referer = $this->get('request')->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @Route("/success.html", name="register_success")
     * @Method({"GET"})
     * @Template()
     *
     * @return array
     */
    public function successAction()
    {
        $request = $this->get('request');

        $username = $request->query->get('username');

        return array(
            'username' => $username
        );
    }

    /**
     * @param $username
     *
     * @Route("/resend/{username}", name="register_resend")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function resendConfirmation($username)
    {
        $manager    = $this->getUserManager();
        $document   = $manager->findUserByUsername($username);

        if (!$document) {
            throw new AccessDeniedException($this->get('translator')->trans('www.user.register.resend.error', array(), 'views'));
        }

        $mailer = $this->get('black_user.mailer');
        $mailer->sendRegisterMessage($document, $document->getConfirmationToken());

        return $this->redirect($this->generateUrl('register_success', array('username' => $username)));
    }

    /**
     * @param $token
     *
     * @Route("/confirmation/{token}", name="register_confirmation")
     * @Method({"GET"})
     * @Template()
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function confirmationAction($token)
    {
        $manager    = $this->getUserManager();
        $document   = $manager->findUserByToken($token);

        if (!$document) {
            throw new AccessDeniedException($this->get('translator')->trans('www.user.register.confirmation.error', array(), 'views'));
        }

        $document->setIsActive(true);
        $document->setConfirmationToken(null);

        $manager->flush();

        $token = new UsernamePasswordToken($document, null, 'main', array('ROLE_USER'));
        $this->get('security.context')->setToken($token);

        return $this->redirect($this->generateUrl('person_me'));
    }

    /**
     * @Route("/suspend", name="register_desactivation")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function suspendAction()
    {
        $security   = $this->get('security.context');
        $user       = $security->getToken()->getUser();
        $manager    = $this->getUserManager();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException($this->get('translator')->trans('www.user.register.suspend.error', array(), 'views'));
        }

        $token = sha1(uniqid().microtime().rand(0, 9999999));

        $user->setIsActive(false);
        $user->setLocked(true);
        $user->setConfirmationToken($token);

        $manager->flush();

        $mailer = $this->get('black_user.mailer');
        $mailer->sendSuspendMessage($user, $token);

        $security->setToken(null);
        $this->get('request')->getSession()->invalidate();
        $this->get('session')->getFlashBag()->add('success', 'www.user.register.suspend.success');

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * @Route("/password-recovery.html", name="register_reactivation_form")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reactivationFormAction()
    {
        $request    = $this->get('request');
        $parameters = $request->request->get('black_user_unlock');

        $form = $this->createForm($this->get('black_user.register.unlock_form.type'));

        if ('POST' === $request->getMethod()) {

            $form->setData($parameters);
            $form->submit($request);

            if ($form->isValid()) {
                $manager    = $this->getUserManager();
                $repository = $manager->getRepository();

                $user  = $repository->loadLockedUser($parameters['_username']);

                if (!is_object($user) || !$user instanceof UserInterface) {
                    $this->get('session')->getFlashBag()->add('error', 'www.user.register.recovery.error');

                    return $this->redirect($this->generateUrl('register_reactivation_form'));
                }

                $user->setIsActive(true);
                $user->setLocked(false);
                $user->setConfirmationToken(null);

                $manager->flush();

                $this->get('session')->getFlashBag()->add('success', 'www.user.register.recovery.success');

                return $this->redirect($this->generateUrl('main_login'));
            }
        }

        return array(
            'form'  => $form->createView()
        );
    }

    /**
     * @param $token
     *
     * @Route("/reactivationlink/{token}", name="register_reactivation_link")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reactivationLinkAction($token)
    {
        $manager    = $this->getUserManager();
        $repository = $manager->getRepository();

        $user  = $repository->loadLockedUser(null, $token);

        if (!is_object($user) || !$user instanceof UserInterface) {
            $this->get('session')->getFlashBag()->add('error', 'www.user.register.reactivation.error');

            return $this->redirect($this->generateUrl('register_reactivation_form'));
        }

        $user->setIsActive(true);
        $user->setLocked(false);
        $user->setConfirmationToken(null);

        $manager->flush();

        $this->get('session')->getFlashBag()->add('success', 'www.user.register.reactivation.success');

        return $this->redirect($this->generateUrl('main_login'));
    }

    /**
     * @Route("/password-lost.html", name="register_password_lost")
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function passwordLostAction()
    {
        $request    = $this->get('request');
        $parameters = $request->request->get('black_user_unlock');

        $form = $this->createForm($this->get('black_user.register.unlock_form.type'));

        if ('POST' === $request->getMethod()) {

            $form->setData($parameters);
            $form->submit($request);

            if ($form->isValid()) {

                $manager    = $this->getUserManager();
                $repository = $manager->getRepository();

                $user  = $repository->loadLostUser($parameters['_username']);

                if (!is_object($user) || !$user instanceof UserInterface) {
                    $this->get('session')->getFlashBag()->add('error', 'www.user.register.lost.error');

                    return $this->redirect($this->generateUrl('main_login'));
                }

                $token = sha1(uniqid().microtime().rand(0, 9999999));
                $user->setConfirmationToken($token);

                $manager->flush();

                $mailer = $this->get('black_user.mailer');
                $mailer->sendLostPasswordMessage($user, $token);

                $this->get('session')->getFlashBag()->add('error', 'www.user.register.lost.success');
                return $this->redirect($this->generateUrl('main_login'));
            }
        }

        return array(
            'form'  => $form->createView()
        );
    }

    /**
     * @param $token
     *
     * @Route("/password-back/{token}", name="register_password_back")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function passwordBackAction($token)
    {
        $manager    = $this->getUserManager();
        $repository = $manager->getRepository();

        $user  = $repository->loadLostUser(null, $token);

        if (!is_object($user) || !$user instanceof UserInterface) {
            $this->get('session')->getFlashBag()->add('error', 'error.user.www.back');

            return $this->redirect($this->generateUrl('register_password_lost'));
        }

        $random = sha1(uniqid().microtime().rand(0, 9999999));
        $user->setRawPassword($random);

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $user->encodePassword($encoder);
        $user->setConfirmationToken(null);

        $manager->flush();

        $mailer = $this->get('black_user.mailer');
        $mailer->sendBackPasswordMessage($user, $random);

        $this->get('session')->getFlashBag()->add('success', 'success.user.www.back');

        return $this->redirect($this->generateUrl('main_login'));
    }

    /**
     * @param $token
     *
     * @Route("/delete/{token}", name="register_delete")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function deleteAction($token)
    {
        $security   = $this->get('security.context');
        $user       = $security->getToken()->getUser();
        $manager    = $this->getUserManager();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('Who are you?');
        }

        $token = $this->get('form.csrf_provider')->isCsrfTokenValid('delete' . $user->getId(), $token);

        if (false === $token) {
            throw new AccessDeniedException('user.exception.delete');
        }

        $mailer = $this->get('black_user.mailer');
        $mailer->sendGoodByeMessage($user);

        $manager->removeAndFlush($user);

        $security->setToken(null);
        $this->get('request')->getSession()->invalidate();
        $this->get('session')->getFlashBag()->add('success', 'success.user.www.delete');

        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * Returns the UserManager
     *
     * @return UserManager
     */
    private function getUserManager()
    {
        return $this->get('black_user.manager.user');
    }
}
