<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Iplog;
use AppBundle\Entity\Blacklist;
use AppBundle\Entity\Econtract;
use AppBundle\Entity\Client;

class CustomerController extends Controller {

    /**
     * Displays the customer homepage
     */
    public function indexAction(Request $request, $token) {
        $em = $this->getDoctrine()->getManager();
        $ipblacklist = $em->getRepository('AppBundle:Etemplate')
                ->findAll();
        $ip = $request->getClientIp();
        if ($token == 'nonexist') {
            return $this->redirectToRoute('customer_accessdenied');
        }
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->findOneBy(array('token' => $token));
        if (!$eContract) {
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip ));
        }
        $isSigned = $eContract->getPatientSigned();
        if($isSigned) {
            return $this->redirectToRoute('customer_alreadysigned', array('id' => $ip ));
        }
        $active = $eContract->getTokenactive();
        if(!$active) {
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip ));
        }

        $today = date("d/F/Y");
        //var_dump($eContract);die;

        return $this->render('AppBundle:customer:index.html.twig', array(
                    'today' => $today, 'eContract' => $eContract,));
    }

    /**
     * Displays the not found homepage
     */
    public function accessDeniedAction($ip) {
        
        
        
        return $this->render('AppBundle:customer:access.html.twig', array(
                    'ip' => $ip,));
    }
    
    /**
     * Displays the already signed homepage
     */
    public function alreadySignedAction($ip) {
        
        return $this->render('AppBundle:customer:alreadysigned.html.twig', array(
                    'ip' => $ip,));
    }
    
    /**
     * Displays the already signed homepage
     */
    public function signednoteAction(Request $request, $token) {
        $em = $this->getDoctrine()->getManager();
        $ip = $request->getClientIp();
        if ($token == 'nonexist') {
            return $this->redirectToRoute('customer_accessdenied');
        }
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->findOneBy(array('token' => $token));
        if (!$eContract) {
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip ));
        }
        
        return $this->render('AppBundle:customer:signednote.html.twig');
    }

    /**
     * Persist drawing to database
     * 
     */
    public function customerAjaxSketchAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('image');
        $contractId = $request->request->get('contractId');


        if ($data) {

            if ($data == "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAZAAAAB4CAYAAADc36SXAAAES0lEQVR4Xu3VsQ0AAAjDMPr/0/yQ2exdLKTsHAECBAgQCAILGxMCBAgQIHAC4gkIECBAIAkISGIzIkCAAAEB8QMECBAgkAQEJLEZESBAgICA+AECBAgQSAICktiMCBAgQEBA/AABAgQIJAEBSWxGBAgQICAgfoAAAQIEkoCAJDYjAgQIEBAQP0CAAAECSUBAEpsRAQIECAiIHyBAgACBJCAgic2IAAECBATEDxAgQIBAEhCQxGZEgAABAgLiBwgQIEAgCQhIYjMiQIAAAQHxAwQIECCQBAQksRkRIECAgID4AQIECBBIAgKS2IwIECBAQED8AAECBAgkAQFJbEYECBAgICB+gAABAgSSgIAkNiMCBAgQEBA/QIAAAQJJQEASmxEBAgQICIgfIECAAIEkICCJzYgAAQIEBMQPECBAgEASEJDEZkSAAAECAuIHCBAgQCAJCEhiMyJAgAABAfEDBAgQIJAEBCSxGREgQICAgPgBAgQIEEgCApLYjAgQIEBAQPwAAQIECCQBAUlsRgQIECAgIH6AAAECBJKAgCQ2IwIECBAQED9AgAABAklAQBKbEQECBAgIiB8gQIAAgSQgIInNiAABAgQExA8QIECAQBIQkMRmRIAAAQIC4gcIECBAIAkISGIzIkCAAAEB8QMECBAgkAQEJLEZESBAgICA+AECBAgQSAICktiMCBAgQEBA/AABAgQIJAEBSWxGBAgQICAgfoAAAQIEkoCAJDYjAgQIEBAQP0CAAAECSUBAEpsRAQIECAiIHyBAgACBJCAgic2IAAECBATEDxAgQIBAEhCQxGZEgAABAgLiBwgQIEAgCQhIYjMiQIAAAQHxAwQIECCQBAQksRkRIECAgID4AQIECBBIAgKS2IwIECBAQED8AAECBAgkAQFJbEYECBAgICB+gAABAgSSgIAkNiMCBAgQEBA/QIAAAQJJQEASmxEBAgQICIgfIECAAIEkICCJzYgAAQIEBMQPECBAgEASEJDEZkSAAAECAuIHCBAgQCAJCEhiMyJAgAABAfEDBAgQIJAEBCSxGREgQICAgPgBAgQIEEgCApLYjAgQIEBAQPwAAQIECCQBAUlsRgQIECAgIH6AAAECBJKAgCQ2IwIECBAQED9AgAABAklAQBKbEQECBAgIiB8gQIAAgSQgIInNiAABAgQExA8QIECAQBIQkMRmRIAAAQIC4gcIECBAIAkISGIzIkCAAAEB8QMECBAgkAQEJLEZESBAgICA+AECBAgQSAICktiMCBAgQEBA/AABAgQIJAEBSWxGBAgQICAgfoAAAQIEkoCAJDYjAgQIEBAQP0CAAAECSUBAEpsRAQIECAiIHyBAgACBJCAgic2IAAECBATEDxAgQIBAEhCQxGZEgAABAgLiBwgQIEAgCQhIYjMiQIAAAQHxAwQIECCQBAQksRkRIECAgID4AQIECBBIAgKS2IwIECBA4AFUYAB5DMHmdgAAAABJRU5ErkJggg==") {
                $html = 'image empty';
                $response = new Response(json_encode($html));
                return $response;
            }

            $eContract = $em->getRepository('AppBundle:Econtract')
                    ->find($contractId);
            $eContract->setClientSignature($data);
            $eContract->setPatientSigned(true);
            $eContract->setClientsignDate(new \DateTime());
            $em->persist($eContract);
            $em->flush();

            $html = $data;
            $response = new Response(json_encode($html));
            return $response;
        }

        $html = 'no data';
        //$html = $data;
        $response = new Response(json_encode($html));
        return $response;
    }

}
