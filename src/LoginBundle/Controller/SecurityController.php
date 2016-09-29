<?php

// src/AppBundle/Controller/SecurityController.php

namespace LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SecurityController extends Controller {

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();


        return $this->render(
                        'security/login.html.twig', array(
                    // last username entered by the user
                    'error' => $error,
                        )
        );
    }
    
    /**
     * @Route("/login", name="login")
     */
    public function customerLoginAction(Request $request) {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();


        return $this->render(
                        'security/customerlogin.html.twig', array(
                    // last username entered by the user
                    'error' => $error,
                        )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction() {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }

}
