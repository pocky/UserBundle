<?php

namespace Black\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Black\Bundle\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/settings.html", name="profile_settings")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function settingsAction()
    {
        $user               = $this->getUser();
        $csrf               = $this->container->get('form.csrf_provider');
        $documentManager    = $this->getUserManager();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('user.exception.settings');
        }

        $formHandler    = $this->get('black_user.form.handler.front_user');
        $process        = $formHandler->process($user);

        if ($process) {
            $documentManager->flush();
        }

        return array(
            'user'  => $user,
            'csrf'  => $csrf,
            'form'  => $formHandler->getForm()->createView()
        );
    }

    /**
     * Returns the User Manager
     *
     * @return DocumentManager
     */
    private function getUserManager()
    {
        return $this->get('black_user.manager.user');
    }
}
