<?php

/*
 * This file is part of the Blackengine package.
 *
 * (c) Alexandre Balmes <albalmes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Black\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller managing the user profile
 *
 * @Route("/admin/user")
 */
class AdminUserController extends Controller
{
    /**
     * Show lists of Users
     *
     * @Method("GET")
     * @Route("/index.html", name="admin_users")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        $manager        = $this->getUserManager();
        $rawDocuments   = $manager->findAll();
        $csrf           = $this->container->get('form.csrf_provider');

        $documents = array();

        foreach ($rawDocuments as $document) {
            $documents[] = array(
                'id'                    => $document->getId(),
                'user.your.username'    => $document->getUsername()
            );
        }

        return array(
            'documents' => $documents,
            'csrf'      => $csrf
        );
    }


    /**
     * Displays a form to create a new User document.
     *
     * @Method({"GET", "POST"})
     * @Route("/new", name="admin_user_new")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function newAction()
    {
        $documentManager    = $this->getUserManager();
        $document           = $documentManager->createUser();

        $formHandler    = $this->get('black_user.form.handler.user');
        $process        = $formHandler->process($document);

        if ($process) {
            $documentManager->persist($document);
            $documentManager->flush();

            return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $document->getId())));
        }

        return array(
            'document'  => $document,
            'form'      => $formHandler->getForm()->createView()
        );
    }

    /**
     * Displays a form to edit an existing User document.
     *
     * @Method({"GET", "POST"})
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     *
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction($id)
    {
        $documentManager = $this->getUserManager();
        $repository = $documentManager->getDocumentRepository();

        $document = $repository->findOneById($id);

        if (!$document) {
            throw $this->createNotFoundException('user.notFound');
        }

        $deleteForm = $this->createDeleteForm($id);

        $formHandler    = $this->get('black_user.form.handler.user');
        $process        = $formHandler->process($document);

        if ($process) {
            $documentManager->flush();

            return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $id)));
        }

        return array(
            'document'      => $document,
            'form'          => $formHandler->getForm()->createView(),
            'delete_form'   => $deleteForm->createView()
        );
    }

    /**
     * Deletes a User document.
     *
     * @Method({"POST", "GET"})
     * @Route("/{id}/delete", name="admin_user_delete")
     * @Secure(roles="ROLE_ADMIN")
     * @param string $id The document ID
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function deleteAction($id, $token = NULL)
    {
        $form       = $this->createDeleteForm($id);
        $request    = $this->getRequest();

        $form->bind($request);

        if (null === $token) {
            $token = $this->get('form.csrf_provider')->isCsrfTokenValid('delete' . $id, $request->query->get('token'));
        }

        if ($form->isValid() || true === $token) {

            $dm         = $this->getUserManager();
            $repository = $dm->getDocumentRepository();
            $document   = $repository->findOneById($id);

            if (!$document) {
                throw $this->createNotFoundException('user.notFound');
            }

            $dm->remove($document);
            $dm->flush();

            $this->get('session')->getFlashBag()->add('success', 'user.flash.success.delete');

        } else {
            $this->get('session')->getFlashBag()->add('failure', 'user.flash.error.delete');
        }

        return $this->redirect($this->generateUrl('admin_users'));
    }

    /**
     * Deletes a User document.
     *
     * @Method({"POST"})
     * @Route("/batch", name="admin_user_batch")
     *
     * @return array
     *
     * @throws \Symfony\Component\Serializer\Exception\InvalidArgumentException If method does not exist
     */
    public function batchAction()
    {
        $request    = $this->getRequest();
        $token      = $this->get('form.csrf_provider')->isCsrfTokenValid('batch', $request->get('token'));

        if (!$ids = $request->get('ids')) {
            $this->get('session')->getFlashBag()->add('failure', 'user.flash.error.batch.select');
            return $this->redirect($this->generateUrl('admin_users'));
        }

        if (!$action = $request->get('batchAction')) {
            $this->get('session')->getFlashBag()->add('failure', 'user.flash.error.batch.action');
            return $this->redirect($this->generateUrl('admin_users'));
        }

        if (!method_exists($this, $method = $action . 'Action')) {
            throw new Exception\InvalidArgumentException(
                sprintf('You must create a "%s" method for action "%s"', $method, $action)
            );
        }

        if (false === $token) {
            $this->get('session')->getFlashBag()->add('failure', 'user.flash.error.batch.csrf');

            return $this->redirect($this->generateUrl('admin_users'));
        }

        foreach ($ids as $id) {
            $this->$method($id, $token);
        }

        return $this->redirect($this->generateUrl('admin_users'));

    }

    private function createDeleteForm($id)
    {
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();

        return $form;
    }

    protected function getUserManager()
    {
        return $this->get('black_user.manager.user');
    }
}
