<?php

namespace LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use LoginBundle\Entity\User;
use LoginBundle\Entity\Customer;
use LoginBundle\Form\UserType;
use LoginBundle\Form\CustomerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends Controller {

    /**
     * 
     * @Route("/register", name="register")
     * @Template("AppBundle:User:register.html.twig")
     */
    public function registerAction(Request $request) {
        $entity = new User();
        $form = $this->createForm(new UserType, $entity);

        $form->remove('isAdmin');
        $form->add('submit', 'submit', array(
            'label' => 'Register',
            'attr' => array('class' => 'btn btn-lg btn-default')
        ));

        $form->handleRequest($request);

        if ('POST' === $request->getMethod()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $this->get('app_bundle.user_manager')
                        ->setUserPassword($entity, $entity->getPassword());
                $entity->setRoles(array('ROLE_USER'));
                $entity->setIsAdmin(false);
                $em->persist($entity);
                $em->flush();

                //!!!! here is the redirection after registration
                $request->getSession()
                        ->getFlashBag()
                        ->add('success', 'You are now registered, please log in');
                return $this->redirect($this->generateUrl('login'));
            }
            return $this->render('LoginBundle:User:register.html.twig', array(
                        'entity' => $entity,
                        'form' => $form->createView(),
            ));
        }

        return $this->render('LoginBundle:User:register.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }
    
}
