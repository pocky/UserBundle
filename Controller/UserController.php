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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Black\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class UserController
 *
 * @package Black\Bundle\UserBundle\Controller
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/settings.html", name="user_settings")
     * @Secure(roles="ROLE_USER")
     * @Template()
     *
     * @return array
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function settingsAction()
    {
        $user               = $this->getUser();
        $csrf               = $this->container->get('form.csrf_provider');
        $documentManager    = $this->getUserManager();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('user.exception.settings');
        }

        $formHandler    = $this->get('black_user.front_user.form.handler');
        $process        = $formHandler->process($user);

        if ($process) {
            $documentManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'www.user.profile.settings.success');

            return $this->redirect(
                $this->generateUrl('main_logout')
            );
        }

        return array(
            'user'  => $user,
            'csrf'  => $csrf,
            'form'  => $formHandler->getForm()->createView()
        );
    }

    /**
     * @return DocumentManager
     */
    protected function getUserManager()
    {
        return $this->get('black_user.manager.user');
    }
}
