<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Iplog;
use AppBundle\Entity\Blacklist;
use AppBundle\Entity\Econtract;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Client;
use AppBundle\Entity\Company;

class CustomerController extends Controller {

    /**
     * Displays the customer homepage
     */
    public function indexAction(Request $request, $token) {
        $em = $this->getDoctrine()->getManager();
        $ipblacklist = $em->getRepository('AppBundle:Etemplate')
                ->findAll();
        $ip = $request->getClientIp();
        //check if token is exist. If not redirect to access denied page
        if ($token == 'nonexist') {
            $error = 'token nonexist';
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip, 'error' => $error));
        }
        //check if the token is valid. If valid, create an ip log in the database
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->findOneBy(array('token' => $token));
        if (!$eContract) {
            $error = 'eContract not found in database';
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip, 'error' => $error));
        } else {
            //check if the Ip log has been created
            $ipLog = $em->getRepository('AppBundle:Iplog')
                    ->findOneBy(array('token' => $token));
            if (!$ipLog) {
                $ipLog = new Iplog();
                $ipLog->setIp($ip);
                $ipLog->setToken($token);
                $ipLog->setCreatedAt(new \DateTime());
                $em->persist($ipLog);
                $em->flush();
            } else {
                //check if DoB submission failed more than 4 times
                $wrongDob = $ipLog->getWrongdob();
                if ($wrongDob > 3) {
                    $error = 'Date of Birth submission failed!';
                    return $this->redirectToRoute('customer_accessdenied', array('id' => $ip, 'error' => $error));
                }
            }
        }


        $isSigned = $eContract->getPatientSigned();
        if ($isSigned) {
            return $this->redirectToRoute('customer_alreadysigned', array('id' => $ip));
        }
        $active = $eContract->getTokenactive();
        if (!$active) {
            $error = 'Contract is not active';
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip, 'error' => $error));
        }

        $savedDob = $eContract->getClient()->getDob();
        $dob = [];
        $form = $this->createFormBuilder($dob)
                ->add('counter', 'hidden', array('read_only' => true))
                ->add('dob', 'birthday', array(
                    'format' => 'dd MM yyyy',
                    'years' => range(1920, date('Y')),
                    'placeholder' => array(
                        'day' => 'Day', 'month' => 'Month', 'year' => 'Year',
            )))
                ->add('save', 'submit', array(
                    'label' => 'Submit',
                    'attr' => array('class' => 'btn btn-danger')
                ))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $dob = $form['dob']->getData();
            $dobString = date_format($dob, 'd-m-Y');
            $savedDobString = date_format($savedDob, 'd-m-Y');

            if ($dobString == $savedDobString) {
                return $this->redirectToRoute('customer_display_contract', array('token' => $token, 'dob' => $dobString));
            } else {
                //User submitted wrong DoB!
                $wrongDob = $ipLog->getWrongdob();
                if (!$wrongDob) {
                    $newWrongDob = 1;
                } else {
                    $newWrongDob = $wrongDob + 1;
                }
                $ipLog->setWrongdob($newWrongDob);
                $em->persist($ipLog);
                $em->flush();
            }
        }

        return $this->render('AppBundle:Customer:checkdob.html.twig', array(
                    'ipLog' => $ipLog, 'token' => $token, 'form' => $form->createView()));
    }

    /**
     * Displays the contract if token is valid and DoB submitted in indexAction
     * @param type $token
     * @param type $dob
     */
    public function displayContractAction($token, $dob) {
        $em = $this->getDoctrine()->getManager();

        $eContract = $em->getRepository('AppBundle:Econtract')
                ->findOneBy(array('token' => $token));


        $today = date("d/F/Y");

        return $this->render('AppBundle:Customer:index.html.twig', array(
                    'today' => $today, 'eContract' => $eContract,));
    }

    /**
     * Displays the contract to the customer. Ajax request, returns the contract view HTML for iframe.
     */
    public function ajaxIframeViewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $econtractId = $request->request->get('econtractId');
        $myContract = $em->getRepository('AppBundle:Econtract')
                ->find($econtractId);
        if (!$myContract) {
            $html = 'error';
            $response = new Response(json_encode($html));
            return $response;
        }
        $rawContent = $myContract->getContent();
        $rawHeading = $myContract->getHeading();
        $rawFirstpage = $myContract->getFirstpage();
        $rawFooter = $myContract->getFooter();

        $html = $this->renderView('AppBundle:Customer:displaycontract.html.twig', array(
            'footer' => $rawFooter, 'firstpage' => $rawFirstpage, 'heading' => $rawHeading, 'html' => $rawContent,));

        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Displays the contract to the customer. Ajax request, returns the contract view HTML for iframe.
     */
    public function customerAjaxPreviewContractAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $econtractId = $request->request->get('econtractId');
        $myContract = $em->getRepository('AppBundle:Econtract')
                ->find($econtractId);
        $html = 'test';

        $signImage = $myContract->getSignature();

        $rawContent = $myContract->getContent();
        $rawHeading = $myContract->getHeading();
        $rawFooter = $myContract->getFooter();
        $rawFirstpage = $myContract->getFirstpage();
        $rawSignpage = $myContract->getSignpage();
        /*
          $realSignPage = $this->renderView('AppBundle:Default:saveclientsignature.html.twig', array(
          'eContract' => $myContract,));
         * 
         */

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);
        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $html = $this->renderView('AppBundle:Default:previewiframe.html.twig', array(
            'signImage' => $signImage, 'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));


        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Displays the not found homepage
     */
    public function accessDeniedAction($ip, $error) {

        return $this->render('AppBundle:Customer:access.html.twig', array(
                    'ip' => $ip, 'error' => $error,));
    }

    /**
     * Displays the already signed homepage
     */
    public function alreadySignedAction($ip) {

        return $this->render('AppBundle:Customer:alreadysigned.html.twig', array(
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
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip));
        }
        $pdf = $eContract->getFilepath();
        if (!$pdf) {
            $this->generatePdf($eContract->getId());
            $this->sendEmail($eContract->getId());
        }

        return $this->render('AppBundle:Customer:signednote.html.twig');
    }
    
    /**
     * Resend the pdf file by email
     */
    public function resendpdfAction($id, $page) {
        $em = $this->getDoctrine()->getManager();
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->find($id);
        if($eContract) {
           $this->sendEmail($id); 
        }
        return $this->redirectToRoute('app_sentcontracts', array('page' => $page, 'resend' => 'success'));
    }

    /**
     * send the email with the attached file
     */
    public function sendEmail($id) {
        $em = $this->getDoctrine()->getManager();
        $myContract = $em->getRepository('AppBundle:Econtract')
                ->find($id);
        if (!$myContract) {
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip));
        }
        $settid = $myContract->getSettid();
        $mySetting = $em->getRepository('AppBundle:Settings')
                ->find($settid);
        $emailTemplate = $em->getRepository('AppBundle:Emailtemplate')
                ->findOneBy(array('tempname' => 'default'));
        $body = $emailTemplate->getBody();
        $subject = $emailTemplate->getSubject();
        $username = $myContract->getUsername();

        $oldemail = $myContract->getEmail();
        $trimmedemail = strtolower(trim($oldemail));
        $email = str_replace(array("\r", "\n"), '', $trimmedemail);
        $cname = $myContract->getClient()->getName();
        $directoryPath = $this->container->getParameter('kernel.root_dir') . '/../web';
        $filePath = $myContract->getFilepath();
        $fullPath = $directoryPath . '/' . $filePath;

        //replace %name% and %username%
        $body = str_replace('%name%', $cname, $body);
        $body = str_replace('%username%', $username, $body);

        $smtp = $mySetting->getSmtp();
        $port = $mySetting->getPort();
        $mssl = $mySetting->getEssl();
        $euser = $mySetting->getEusername();
        $epass = $mySetting->getEpassword();
        $auth = $mySetting->getAuth();
        $fromname = $mySetting->getFromname();

        if ($auth) {
            $transport = \Swift_SmtpTransport::newInstance($smtp, $port)
                    ->setUsername($euser)
                    ->setPassword($epass)
                    ->setAuthMode('PLAIN')
            ;
        } else {
            $transport = \Swift_SmtpTransport::newInstance($smtp, $port, $mssl)
                    ->setUsername($euser)
                    ->setPassword($epass)
                    ->setAuthMode('PLAIN')
            ;
        }
        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance($subject)
                ->setFrom(array($euser => $fromname))
                ->setTo(array($email => $cname))
                //->setBcc(array('sent@dentistabroad.co.uk' => 'Sent'))
                ->setBody($body, 'text/html')
        ;
        $message->attach(\Swift_Attachment::fromPath($fullPath));
        $mailer->getTransport()->start();
        $mailer->send($message);
        $mailer->getTransport()->stop();

        return true;
    }

    /**
     * Generate the pdf file TEST ONLY
     */
    /*
      public function generatePdftestAction($id) {
      $em = $this->getDoctrine()->getManager();

      $myContract = $em->getRepository('AppBundle:Econtract')
      ->find($id);
      if (!$myContract) {
      return $this->redirectToRoute('customer_accessdenied', array('id' => $ip));
      }
      $clientImage = $myContract->getClientSignature();

      $rawContent = $myContract->getContent();
      $rawHeading = $myContract->getHeading();
      $rawFooter = $myContract->getFooter();
      $rawFirstpage = $myContract->getFirstpage();
      $rawSignpage = $myContract->getSignpage();
      $realSignPage = $this->renderView('AppBundle:Customer:saveclientsignature.html.twig', array(
      'eContract' => $myContract,));

      $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
      $footer = '<div class="footer">' . $rawFooter . '</div></div>';
      $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
      $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


      $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
      $content = str_replace('[[page break]]', $pageBREAK, $content);

      $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
      $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '' . $realSignPage . '</div>' . $footer;

      $html = $this->renderView('AppBundle:Default:previewnewwindow.html.twig', array(
      'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));
      $signImage = $myContract->getSignature();



      return $this->render('AppBundle:Customer:pdfdisplay.html.twig', array(
      'html' => $html, 'clientImage' => $clientImage, 'signImage' => $signImage,
      ));

      /*

      //generate pdf here
      $nowDate = new \DateTime();
      $nowString = $nowDate->format('d_m_Y_h_i_s');
      $directoryPath = $this->container->getParameter('kernel.root_dir') . '/../web/Files/' . $id;
      if (!file_exists($directoryPath)) {
      mkdir($directoryPath, 0777, true);
      }
      $clientName = $myContract->getClient()->getName();
      $filename = $clientName . '-' . $id . '-' . $nowString . '.pdf';
      $fullPath = $directoryPath . '/' . $filename;
      if (file_exists($fullPath)) {
      rename($fullPath, $fullPath . '_old');
      }

      $this->get('knp_snappy.pdf')->generateFromHtml(
      $this->renderView(
      'AppBundle:Customer:pdfdisplay.html.twig', array(
      'html' => $html, 'clientImage' => $clientImage, 'signImage' => $signImage,
      )
      ), $fullPath
      );
      //Generate pdf end!
      //save the filepath to the database
      $relativePath = 'Files/' . $id . '/' . $filename;
      $myContract->setFilepath($relativePath);
      $em->persist($myContract);
      $em->flush();
      return true;
      }
     * * 
     * 
     */

    /**
     * Generate the pdf file
     */
    public function generatePdf($id) {
        $em = $this->getDoctrine()->getManager();

        $myContract = $em->getRepository('AppBundle:Econtract')
                ->find($id);
        if (!$myContract) {
            return $this->redirectToRoute('customer_accessdenied', array('id' => $ip));
        }
        $clientImage = $myContract->getClientSignature();

        $rawContent = $myContract->getContent();
        $rawHeading = $myContract->getHeading();
        $rawFooter = $myContract->getFooter();
        $rawFirstpage = $myContract->getFirstpage();
        $rawSignpage = $myContract->getSignpage();
        $realSignPage = $this->renderView('AppBundle:Customer:saveclientsignature.html.twig', array(
            'eContract' => $myContract,));

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '' . $realSignPage . '</div>' . $footer;

        $html = $this->renderView('AppBundle:Default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));
        $signImage = $myContract->getSignature();


        //generate pdf start
        $nowDate = new \DateTime();
        $nowString = $nowDate->format('d_m_Y_h_i_s');
        $directoryPath = $this->container->getParameter('kernel.root_dir') . '/../web/Files/' . $id;
        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
        $clientName = $myContract->getClient()->getName();
        $filename = $clientName . '-' . $id . '-' . $nowString . '.pdf';
        $fullPath = $directoryPath . '/' . $filename;
        if (file_exists($fullPath)) {
            rename($fullPath, $fullPath . '_old');
        }

        $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView(
                        'AppBundle:Customer:pdfdisplay.html.twig', array(
                    'html' => $html, 'clientImage' => $clientImage, 'signImage' => $signImage,
                        )
                ), $fullPath
        );
        //Generate pdf end!
        //save the filepath to the database
        $relativePath = 'Files/' . $id . '/' . $filename;
        $myContract->setFilepath($relativePath);
        $em->persist($myContract);
        $em->flush();
        return true;
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
        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * About page display
     */
    public function aboutAction() {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AppBundle:Company')
                ->findOneBy(array('active' => 1));
        if (!$company) {
            $company = false;
        }
        return $this->render('AppBundle:Customer:about.html.twig', array(
                    'company' => $company,
        ));
    }

    /**
     * Send message from website to the system admin. Use the first email settings as mail server
     */
    public function contactAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $company = $em->getRepository('AppBundle:Company')
                ->findOneBy(array('active' => 1));
        if (!$company) {
            $company = false;
        }
        $contact = [];

        $form = $this->createFormBuilder($contact)
                ->add('message', 'textarea', array(
                    'label' => 'Message',
                    'required' => true, "attr" => array("col" => "10", "row" => 10)))
                ->add('name', 'text', array(
                    'label' => 'Name',
                    'required' => true))
                ->add('phone', 'text', array(
                    'label' => 'Phone',
                    'required' => true))
                ->add('email', 'email', array(
                    'label' => 'Email',
                    'required' => true))
                ->add('save', 'submit', array('label' => 'Send message'))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $textMessage = $form["message"]->getData();
            $textName = $form["name"]->getData();
            $textPhone = $form["phone"]->getData();
            $textEmail = $form["email"]->getData();

            $messageBody = ' ' . $textMessage . ' From: ' . $textName . ' Phone:' . $textPhone . ' Email: ' . $textEmail;
            $fromname = 'Esignature';
            $mySettingArray = $em->getRepository('AppBundle:Settings')
                    ->findAll();
            //var_dump($mySetting);die;
            $mySetting = $mySettingArray[0];
            $smtp = $mySetting->getSmtp();
            $port = $mySetting->getPort();
            $mssl = $mySetting->getEssl();
            $euser = $mySetting->getEusername();
            $epass = $mySetting->getEpassword();
            $auth = $mySetting->getAuth();
            //$fromname = $mySetting->getFromname();
            if ($auth) {
                $transport = \Swift_SmtpTransport::newInstance($smtp, $port)
                        ->setUsername($euser)
                        ->setPassword($epass)
                        ->setAuthMode('PLAIN')
                ;
            } else {
                $transport = \Swift_SmtpTransport::newInstance($smtp, $port, $mssl)
                        ->setUsername($euser)
                        ->setPassword($epass)
                        ->setAuthMode('PLAIN')
                ;
            }
            $mailer = \Swift_Mailer::newInstance($transport);
            $message = \Swift_Message::newInstance('New message from eSignature' . $textName)
                    ->setFrom(array($euser => $fromname))
                    ->setTo('petercsatai@gmail.com')
                    ->setBody($messageBody, 'text/html')
            ;
            $mailer->getTransport()->start();
            $mailer->send($message);
            $mailer->getTransport()->stop();

            $this->addFlash(
                    'notice', 'Your message has been sent!'
            );
            return $this->redirectToRoute('customer_contact');
        }


        return $this->render('AppBundle:Customer:contact.html.twig', array(
                    'form' => $form->createView(), 'company' => $company,
        ));
    }

}
