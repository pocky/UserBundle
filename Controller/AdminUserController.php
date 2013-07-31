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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class AdminUserController
 * Controller managing the user profile
 *
 * @package Black\Bundle\UserBundle\Controller
 * @author  Alexandre Balmes <albalmes@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php MIT
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
     * 
     * @return array
     */
    public function indexAction()
    {
        $csrf           = $this->container->get('form.csrf_provider');

        $keys = array(
            'id',
            'user.admin.user.username.text'
        );

        return array(
            'keys'      => $keys,
            'csrf'      => $csrf
        );
    }

    /**
     * Show lists of Users
     *
     * @Method("GET")
     * @Route("/list.json", name="admin_users_json")
     * @Secure(roles="ROLE_ADMIN")
     * 
     * @return Response
     */
    public function ajaxListAction()
    {
        $documentManager    = $this->getManager();
        $repository         = $documentManager->getRepository();

        $rawDocuments       = $repository->findAll();

        $documents = array('aaData' => array());
        foreach ($rawDocuments as $document) {
            $documents['aaData'][] = array(
                $document->getId(),
                $document->getUserName(),
                null
            );
        }

        return new Response(json_encode($documents));
    }

    /**
     * Displays a form to create a new User document.
     *
     * @Method({"GET", "POST"})
     * @Route("/new", name="admin_user_new")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     * 
     * @return array
     */
    public function newAction()
    {
        $documentManager    = $this->getManager();
        $document           = $documentManager->createInstance();

        $formHandler    = $this->get('black_user.user.form.handler');
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
     * @param string $id The document ID
     *
     * @Method({"GET", "POST"})
     * @Route("/{id}/edit", name="admin_user_edit")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function editAction($id)
    {
        $documentManager = $this->getManager();
        $repository = $documentManager->getRepository();

        $document = $repository->findOneById($id);

        if (!$document) {
            throw $this->createNotFoundException('user.notFound');
        }

        $deleteForm = $this->createDeleteForm($id);

        $formHandler    = $this->get('black_user.user.form.handler');
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
     * @param string $id
     * @param null $token
     * 
     * @Method({"POST", "GET"})
     * @Route("/{id}/delete/{token}", name="admin_user_delete")
     * @Secure(roles="ROLE_ADMIN")
     *
     * @return array
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If document doesn't exists
     */
    public function deleteAction($id, $token = null)
    {
        $form       = $this->createDeleteForm($id);
        $request    = $this->getRequest();

        $form->bind($request);

        if (null !== $token) {
            $token = $this->get('form.csrf_provider')->isCsrfTokenValid('delete', $token);
        }

        if ($form->isValid() || true === $token) {

            $dm         = $this->getManager();
            $document   = $dm->findUserById($id);

            if (!$document) {
                throw $this->createNotFoundException('user.notFound');
            }

            $dm->removeAndFlush($document);
            $dm->flush();

            $this->get('session')->getFlashBag()->add('success', 'success.user.admin.delete');

        } else {
            $this->get('session')->getFlashBag()->add('error', 'error.user.admin.delete');
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
            $this->get('session')->getFlashBag()->add('error', 'error.user.admin.batch.select');

            return $this->redirect($this->generateUrl('admin_users'));
        }

        if (!$action = $request->get('batchAction')) {
            $this->get('session')->getFlashBag()->add('error', 'error.user.admin.batch.action');

            return $this->redirect($this->generateUrl('admin_users'));
        }

        if (!method_exists($this, $method = $action . 'Action')) {
            throw new Exception\InvalidArgumentException(
                sprintf('You must create a "%s" method for action "%s"', $method, $action)
            );
        }

        if (false === $token) {
            $this->get('session')->getFlashBag()->add('error', 'error.user.admin.batch.crsf');

            return $this->redirect($this->generateUrl('admin_users'));
        }

        foreach ($ids as $id) {
            $this->$method($id, $this->get('form.csrf_provider')->generateCsrfToken('delete'));
        }

        return $this->redirect($this->generateUrl('admin_users'));

    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();

        return $form;
    }

    /**
     * @return object
     */
    protected function getManager()
    {
        return $this->get('black_user.manager.user');
    }
}
