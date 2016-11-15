<?php

namespace LoginBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LoginBundle\Entity\User;
use LoginBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/admin")
 */
class UserController extends Controller {

    /**
     * Lists all User entities.
     * 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LoginBundle:User')->findAll();
        //var_dump($entities);die;
        return $this->render('LoginBundle:User:index.html.twig', array(
                    'entities' => $entities,));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="admin_create")
     * @Method("POST")
     * @Template("AppBundle:User:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('app_bundle.user_manager')
                    ->setUserPassword($entity, $entity->getPassword());
            $role = ($form->get('isAdmin')->getData()) ? 'ROLE_ADMIN' : 'ROLE_USER';
            $entity->setRoles(array($role));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_show', array('id' => $entity->getId())));
        }
        return $this->render('LoginBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity) {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('admin_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array(
                'class' => 'btn btn-large btn-default'
        )));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction() {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('LoginBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoginBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return $this->render('LoginBundle:User:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoginBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        if (in_array('ROLE_SUPER_ADMIN', $entity->getRoles())) {
            $request->getSession()
                    ->getFlashbag()
                    ->add('danger', 'Super Admin Profiles can not be edited in the Admin Area');
            return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('LoginBundle:User:edit.html.twig',  array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     *   Displays a form to edit the password for existing User Entity
     *
     * @Route("/{id}/password", name="admin_password")
     * @Method("GET")
     * @Template()
     */
    public function passwordAction(Request $request, $id) {
        //get the entity manager
        //use the em to get our entity for the id given
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoginBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Undable to find User entity!');
        }
        if (in_array('ROLE_SUPER_ADMIN', $entity->getRoles())) {
            $request->getSession()
                    ->getFlashbag()
                    ->add('danger', 'Super Admin Profiles can not be edited in the Admin Area');
            return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
        }

        $passwordForm = $this->createPasswordForm($entity);

        // pass objects to the view
        // because we are using the namespaced Template Class 
        // there is no need to call $this->render()
        return array(
            'entity' => $entity,
            'password_form' => $passwordForm->createView(),
        );
    }

    /**
     *   Creates a change password form!
     * @param User $entity
     * @return \Symfony\Component\Form\Form $passwordForm
     */
    private function createPasswordForm(User $entity) {
        // tell symfony to create a Form using the UserFormType
        // and the entity we pulled from the em a few lines back
        // must pass in the route where the form will post to
        // must also pass the id!
        $passwordForm = $this->createForm(new UserType, $entity, array(
            'action' => $this->generateUrl(
                    'admin_password_update', array('id' => $entity->getId())
            ),
            'method' => 'PUT',
        ));

        // remove the fields we don't want from the FormType Object
        $passwordForm->remove('username');
        $passwordForm->remove('firstname');
        $passwordForm->remove('lastname');
        $passwordForm->remove('email');

        // add a submit button
        $passwordForm->add('submit', 'submit', array(
            'label' => 'Save New Password',
            'attr' => array('class' => 'btn btn-default'),
        ));
        return $passwordForm;
    }

    /**
     * Update password by id
     * 
     */
    public function passwordUpdateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('LoginBundle:User')
                ->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $passwordForm = $this->createPasswordForm($entity);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isValid()) {
            $this->get('app_bundle.user_manager')->setUserpassword($entity, $entity->getPassword());
            $em->flush();

            return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
        }

        return $this->render('LoginBundle:User:password.html.twig', array(
            'entity' => $entity,
            'password_form' => $passwordForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity) {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('admin_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->remove('password');
        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LoginBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $role = ($editForm->get('isAdmin')->getData()) ? 'ROLE_ADMIN' : 'ROLE_USER';
            $entity->setRoles(array($role));
            //var_dump($entity);die;
            $em->flush();

            return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
        }

        return $this->render('LoginBundle:User:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LoginBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }
            if (in_array('ROLE_SUPER_ADMIN', $entity->getRoles())) {
                $request->getSession()
                        ->getFlashbag()
                        ->add('danger', 'Super Admin Profiles can not be deleted in the Admin Area');
                return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('admin_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'label' => 'Delete',
                            'attr' => array(
                                'class' => 'btn btn-danger'
                    )))
                        ->getForm()
        ;
    }
}
