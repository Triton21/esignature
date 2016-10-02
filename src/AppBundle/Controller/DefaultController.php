<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Paginator\Paginator;
use AppBundle\Form\EtemplateType;
use AppBundle\Form\ClientType;
use AppBundle\Form\EcontractType;
use AppBundle\Form\TemplatesettingsType;
use AppBundle\Form\SettingsType;
use AppBundle\Form\SettingssmtpType;
use AppBundle\Entity\Etemplate;
use AppBundle\Entity\Settings;
use AppBundle\Entity\Product;
use AppBundle\Entity\Emailtemplate;
use AppBundle\Entity\Templatesettings;
use AppBundle\Entity\Econtract;
use AppBundle\Entity\Selfsignature;
use AppBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller {

    /**
     * Displays the homepage
     */
    public function indexAction() {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_CUSTOMER', $roles, true)) {
            return $this->redirectToRoute('customer_index');
        }

        $name = $user->getUsername();
        // replace this example code with whatever you need
        return $this->render('AppBundle:default:index.html.twig', array(
                    'name' => $name,));
    }

    /**
     * List of templates
     */
    public function templateAction($page) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $name = $user->getUsername();

        $em = $this->getDoctrine()->getManager();
        $limit = 20;
        $offset = ($limit * $page) - $limit;
        $countAll = $em->getRepository('AppBundle:Etemplate')
                ->countAll();

        //fetch templates with limit and offset
        $etemplates = $em->getRepository('AppBundle:Etemplate')
                ->findByLimitOffset($offset, $limit);

        $findActiveSignature = $em->getRepository('AppBundle:Selfsignature')
                ->findOneBy(array('usethis' => 1));
        $signatureImage = $findActiveSignature->getImage();
        $pager = new Paginator($page, $countAll, $limit);
        return $this->render('AppBundle:default:template.html.twig', array(
                    'signatureImage' => $signatureImage, 'etemplates' => $etemplates, 'pager' => $pager, 'name' => $name,));
    }

    /**
     * Create the new template form and persist the data to database. Form content displayed with CK editor.
     * 
     */
    public function createTemplateAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        $findActiveSettings = $em->getRepository('AppBundle:Templatesettings')
                ->findOneBy(array('usethis' => 1));

        $findActiveSignature = $em->getRepository('AppBundle:Selfsignature')
                ->findOneBy(array('usethis' => 1));
        $signatureImage = $findActiveSignature->getImage();

        $etemplate = new Etemplate();
        if ($findActiveSettings && $findActiveSignature) {
            $etemplate->setHeading($findActiveSettings->getHeading());
            $etemplate->setFooter($findActiveSettings->getFooter());
            $etemplate->setFirstpage($findActiveSettings->getFirstpage());
            $etemplate->setSignpage($findActiveSettings->getSignpage());
            $etemplate->setSignature($signatureImage);
        }

        $form = $this->createForm(new EtemplateType(), $etemplate);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $etemplate->setUsername($name);
            $em->persist($etemplate);
            $em->flush();
            return $this->redirectToRoute('app_template');
        }

        return $this->render('AppBundle:default:createTemplate.html.twig', array(
                    'signatureImage' => $signatureImage, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Edit previously saved template.
     * 
     */
    public function editTemplateAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $etemplate = $em->getRepository('AppBundle:Etemplate')
                ->findOneBy(array('id' => $id));
        $username = $etemplate->getUsername();
        $user = $this->getUser();
        $name = $user->getUsername();
        if ($name != $username) {
            return $this->redirectToRoute('app_template');
        }
        $selfSignatureAll = $em->getRepository('AppBundle:Selfsignature')
                ->findAll();
        foreach ($selfSignatureAll as $selfSignatureObj) {
            $selfSignature[$selfSignatureObj->getId()] = $selfSignatureObj->getSignname();
        }

        $form = $this->createForm(new EtemplateType(), $etemplate);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $etemplate->setUsername($name);
            $em->persist($etemplate);

            $em->flush();
            return $this->redirectToRoute('app_template');
        }
        return $this->render('AppBundle:default:editTemplate.html.twig', array(
                    'etemplate' => $etemplate, 'id' => $id, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Create headings, firstpage and footer as base settings for all template
     * and persist the data to database. Form content displayed with CK editor.
     * 
     */
    public function templateSettingsAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        $savedSettings = $em->getRepository('AppBundle:Templatesettings')
                ->findAll();
        $signatureList = $em->getRepository('AppBundle:Selfsignature')
                ->findAll();

        foreach ($signatureList as $signatureObject) {
            $signatureArray[$signatureObject->getId()] = $signatureObject->getSignname();
        }

        $templateSettings = new Templatesettings();
        $initHeading = '<br><br><hr>';
        $initFooter = '<hr>';

        $initSignpage = $this->renderView('AppBundle:default:signlastpage.html.twig');
        $form = $this->createForm(new TemplatesettingsType($initHeading, $initFooter, $initSignpage, $signatureArray), $templateSettings);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $findActive = $em->getRepository('AppBundle:Templatesettings')
                    ->findOneBy(array('usethis' => 1));
            if ($findActive) {
                $findActive->setUsethis(0);
                $em->persist($findActive);
            }
            $templateSettings->setUsername($name);
            $signId = $form["signatureid"]->getData();
            $mySignature = $em->getRepository('AppBundle:Selfsignature')
                    ->find($signId);
            //var_dump($mySignature);die;
            $signPage = $this->renderView('AppBundle:default:savelastpage.html.twig', array(
                'mySignature' => $mySignature));
            /*
              $templateSettings->setCompanyname($companyName);
              $templateSettings->setAddressfirstline($address);
              $templateSettings->setAddresstown($town);
              $templateSettings->setPostcode($postcode);
             * 
             */
            $templateSettings->setSignpage($signPage);
            $templateSettings->setUsethis(1);
            $em->persist($templateSettings);

            $em->flush();
            return $this->redirectToRoute('app_templatesettings');
        }

        return $this->render('AppBundle:default:templatesettings.html.twig', array(
                    'savedSettings' => $savedSettings, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Edit saved settings if username == saved username
     * @param type $id
     * @return type
     */
    public function settingsEditAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        /*
          $findActive = $em->getRepository('AppBundle:Selfsignature')
          ->findOneBy(array('usethis' => 1));
          $signname = $findActive->getSignname();
          $position = $findActive->getPosition();
          $signatureImage = $findActive->getImage();
         * 
         */
        $signatureList = $em->getRepository('AppBundle:Selfsignature')
                ->findAll();

        foreach ($signatureList as $signatureObject) {
            $signatureArray[$signatureObject->getId()] = $signatureObject->getSignname();
        }


        $templateSettings = $em->getRepository('AppBundle:Templatesettings')
                ->find($id);
        // var_dump($templateSettings->getSignatureid());die;

        if ($templateSettings->getSignatureid()) {
            $signature = $em->getRepository('AppBundle:Selfsignature')
                    ->find($templateSettings->getSignatureid());
            $signatureImage = $signature->getImage();
        } else {
            $signatureImage = '';
        }

        $initHeading = $templateSettings->getHeading();
        $initFooter = $templateSettings->getFooter();
        $initSignpage = $templateSettings->getSignpage();


        $form = $this->createForm(new TemplatesettingsType($initHeading, $initFooter, $initSignpage, $signatureArray), $templateSettings);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $templateSettings->setUsername($name);
            $signId = $form["signatureid"]->getData();
            $mySignature = $em->getRepository('AppBundle:Selfsignature')
                    ->find($signId);
            //var_dump($mySignature);die;
            $signPage = $this->renderView('AppBundle:default:savelastpage.html.twig', array(
                'mySignature' => $mySignature));
            $templateSettings->setSignpage($signPage);
            $em->persist($templateSettings);



            $em->flush();
            return $this->redirectToRoute('app_templatesettings');
        }

        return $this->render('AppBundle:default:templatesettingsedit.html.twig', array(
                    'signatureImage' => $signatureImage, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewNewWindowAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');
        $signId = $request->request->get('signId');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        //get the signature png image and pass it to the template
        $findSignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($signId);
        $signImage = $findSignature->getImage();

        if ($rawContent === '') {
            $content = false;
        }

        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage));

        $myJson = new JsonResponse(array('html' => $html, 'image' => $signImage));
        return $myJson;
    }

    /**
     * Ajax request, returns the preview HTML code of the unsaved form data
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewNewWindowTemplateAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');
        $rawSignature = $request->request->get('signature');
        $signId = $request->request->get('signId');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $findSignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($signId);
        $signImage = $findSignature->getImage();

        if ($rawContent === '') {
            $content = false;
        }

        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage));

        $myJson = new JsonResponse(array('html' => $html, 'image' => $rawSignature));
        return $myJson;
    }

    public function testpageAction() {

        return $this->render('AppBundle:default:testTemplate.html.twig');
    }

    public function signaturepageAction() {
        $em = $this->getDoctrine()->getManager();
        $findActive = $em->getRepository('AppBundle:Selfsignature')
                ->findOneBy(array('usethis' => 1));
        $signname = $findActive->getSignname();
        $position = $findActive->getPosition();

        return $this->render('AppBundle:default:signaturepage.html.twig', array('signname' => $signname, 'position' => $position));
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data. Code will be displayed on the same page with iframe.
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');
        $signId = $request->request->get('signId');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';

        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $findSignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($signId);
        $signImage = $findSignature->getImage();

        if ($rawContent === '') {
            $content = false;
        }

        $html = $this->renderView('AppBundle:default:previewiframe.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage,));

        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewNewWindowNewTemplateAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        //$signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;
        //get the signature png image and pass it to the template
        $findActive = $em->getRepository('AppBundle:Templatesettings')
                ->findOneBy(array('usethis' => 1));
        $signpageCode = $findActive->getSignpage();
        $signpage = $heading . '<div class="pagebody">' . $signpageCode . '</div>' . $footer;


        $signId = $findActive->getSignatureid();
        $mySignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($signId);
        $signImage = $mySignature->getImage();


        if ($rawContent === '') {
            $content = false;
        }


        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage));

        //$html = 'something';
        //$signImage = 'image';

        $myJson = new JsonResponse(array('html' => $html, 'image' => $signImage));
        return $myJson;
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data. Code will be displayed on the same page with iframe.
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewNewTemplateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';

        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $findActive = $em->getRepository('AppBundle:Selfsignature')
                ->findOneBy(array('usethis' => 1));
        $signImage = $findActive->getImage();

        if ($rawContent === '') {
            $content = false;
        }

        $html = $this->renderView('AppBundle:default:previewiframe.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage,));

        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data. Code will be displayed on the same page with iframe.
     * @param Request $request
     * @return Response
     */
    public function ajaxPreviewTemplateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $rawContent = $request->request->get('content');
        $rawHeading = $request->request->get('heading');
        $rawFooter = $request->request->get('footer');
        $rawFirstpage = $request->request->get('firstpage');
        $rawSignpage = $request->request->get('signpage');
        $tempId = $request->request->get('tempId');

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';

        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $myTemplate = $em->getRepository('AppBundle:Etemplate')
                ->find($tempId);
        $signImage = $myTemplate->getSignature();

        if ($rawContent === '') {
            $content = false;
        }

        $html = $this->renderView('AppBundle:default:previewiframe.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage));

        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Ajax request, returns the preview HTML code with the unsaved form data
     * @param Request $request
     * @return Response
     */
    public function ajaxOpenSettingsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $settId = $request->request->get('myid');

        $mySettings = $em->getRepository('AppBundle:Templatesettings')
                ->find($settId);
        $signId = $mySettings->getSignatureid();
        $mySignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($signId);
        $signImage = $mySignature->getImage();

        $content = false;
        $rawHeading = $mySettings->getHeading();
        $rawFooter = $mySettings->getFooter();
        $rawFirstpage = $mySettings->getFirstpage();
        $rawSignpage = $mySettings->getSignpage();

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;


        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage));

        $myJson = new JsonResponse(array('html' => $html, 'image' => $signImage));
        return $myJson;
    }

    /**
     * Ajax request, returns the preview HTML code of the saved templates
     * @param Request $request
     * @return Response
     */
    public function ajaxOpenTemplateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $settId = $request->request->get('myid');

        $myTemplate = $em->getRepository('AppBundle:Etemplate')
                ->find($settId);
        //$myName = $myTemplate->getTempname();

        $rawContent = $myTemplate->getContent();
        $rawHeading = $myTemplate->getHeading();
        $rawFooter = $myTemplate->getFooter();
        $rawFirstpage = $myTemplate->getFirstpage();
        $rawSignpage = $myTemplate->getSignpage();


        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;



        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));
        $signImage = $myTemplate->getSignature();
        $myJson = new JsonResponse(array('html' => $html, 'image' => $signImage));
        return $myJson;
    }

    /**
     * Ajax request, returns the preview HTML code of the saved templates
     * @param Request $request
     * @return Response
     */
    public function ajaxOpenContractAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $contractId = $request->request->get('myid');

        $myContract = $em->getRepository('AppBundle:Econtract')
                ->find($contractId);
        $clientImage = $myContract->getClientSignature();
        /*
          //$html = 'Econtract ajax' . $contractId;
          //$myName = $myTemplate->getTempname();
          $clientName = $myContract->getClient()->getName();
          $addressFirstLine = $myContract->getClient()->getAddressfirstline();
          $addressTown = $myContract->getClient()->getAddresstown();
          $postcode = $myContract->getClient()->getPostcode();
          /*
          $todayObj = $myContract->getCreatedAt();
          $contractDate = $todayObj->format('d-m-Y');
         * 
         */


        $rawContent = $myContract->getContent();
        $rawHeading = $myContract->getHeading();
        $rawFooter = $myContract->getFooter();
        $rawFirstpage = $myContract->getFirstpage();
        $rawSignpage = $myContract->getSignpage();
        $realSignPage = $this->renderView('AppBundle:default:saveclientsignature.html.twig', array(
            'eContract' => $myContract,));

        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '' . $realSignPage . '</div>' . $footer;

        $html = $this->renderView('AppBundle:default:previewnewwindow.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));
        $signImage = $myContract->getSignature();
        /*
          $html = str_replace('%%clientname%%', $clientName, $html);
          $html = str_replace('%%firstlineaddress%%', $addressFirstLine, $html);
          $html = str_replace('%%addresstown%%', $addressTown, $html);
          $html = str_replace('%%postcode%%', $postcode, $html);
          $html = str_replace('%%date%%', $contractDate, $html);
         * 
         */

        $myJson = new JsonResponse(array('html' => $html, 'image' => $signImage, 'clientImage' => $clientImage,));
        return $myJson;


        //$response = new Response(json_encode($html));
        //return $response;
    }

    /**
     * Ajax request, returns the preview HTML code of the saved templates
     * @param Request $request
     * @return Response
     */
    public function ajaxOpenTemplateIframeAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $settId = $request->request->get('myid');
        $clientId = $request->request->get('clientId');


        $myTemplate = $em->getRepository('AppBundle:Etemplate')
                ->find($settId);
        //$myName = $myTemplate->getTempname();
        $client = $em->getRepository('AppBundle:Client')
                ->find($clientId);
        $clientName = $client->getName();
        $addressFirstLine = $client->getAddressfirstline();
        $addressTown = $client->getAddresstown();
        $postcode = $client->getPostcode();
        $today = date("d/F/Y");

        $rawContent = $myTemplate->getContent();
        $rawHeading = $myTemplate->getHeading();
        $rawFooter = $myTemplate->getFooter();
        $rawFirstpage = $myTemplate->getFirstpage();
        $rawSignpage = $myTemplate->getSignpage();


        $heading = '<div class="relative"><div class="header">' . $rawHeading . '</div>';
        $footer = '<div class="footer">' . $rawFooter . '</div></div>';
        $startContent = $heading . '<div class="pagebody">' . $rawContent . '</div>' . $footer;
        $pageBREAK = '</div><div class="footer">' . $rawFooter . '</div></div><div class="relative"><div class="header">' . $rawHeading . '</div><div class="pagebody">';


        $content = str_replace('<p>[[page break]]', $pageBREAK, $startContent);
        $content = str_replace('[[page break]]', $pageBREAK, $content);

        $firstpage = $heading . '<div class="pagebody">' . $rawFirstpage . '</div>' . $footer;
        $signpage = $heading . '<div class="pagebody">' . $rawSignpage . '</div>' . $footer;

        $signImage = $myTemplate->getSignature();

        $html = $this->renderView('AppBundle:default:previewiframe.html.twig', array(
            'signImage' => $signImage, 'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage,));
        $html = str_replace('%%clientname%%', $clientName, $html);
        $html = str_replace('%%firstlineaddress%%', $addressFirstLine, $html);
        $html = str_replace('%%addresstown%%', $addressTown, $html);
        $html = str_replace('%%postcode%%', $postcode, $html);
        $html = str_replace('%%date%%', $today, $html);


        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Change the settings as Active, all other settings will be inactive
     * @param type $id
     * @return type
     */
    public function useSettingsAction($id) {
        $em = $this->getDoctrine()->getManager();

        $findActive = $em->getRepository('AppBundle:Templatesettings')
                ->findOneBy(array('usethis' => 1));
        if ($findActive) {
            $findActive->setUsethis(0);
            $em->persist($findActive);
        }
        $templateSettings = $em->getRepository('AppBundle:Templatesettings')
                ->find($id);
        $templateSettings->setUsethis(1);
        $em->persist($templateSettings);
        $em->flush();
        return $this->redirectToRoute('app_templatesettings');
    }

    /**
     * Delete settings if username == saved username
     * @param type $id
     * @return type
     */
    public function settingsDeleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        $templateSettings = $em->getRepository('AppBundle:Templatesettings')
                ->find($id);
        if ($templateSettings) {
            $username = $templateSettings->getUsername();
            if ($username == $name) {
                $em->remove($templateSettings);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_templatesettings');
    }

    /**
     * Delete template if username == saved username
     * @param type $id
     * @return type
     */
    public function deleteTemplateAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        $template = $em->getRepository('AppBundle:Etemplate')
                ->find($id);
        if ($template) {
            $username = $template->getUsername();
            if ($username == $name) {
                $em->remove($template);
                $em->flush();
            }
        }

        return $this->redirectToRoute('app_template');
    }

    /**
     * Create the new template form and persist the data to database. Form content displayed with CK editor.
     * 
     */
    public function previewIframeAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $user = $this->getUser();
        $name = $user->getUsername();
        $content = true;

        return $this->render('AppBundle:default:previewiframe.html.twig', array(
                    'content' => $content,));
    }

    /**
     * Ajax request initialise the template preview with base A4 html code
     * @return Response
     */
    public function initpreviewAction() {

        $content = false;
        $firstpage = false;
        $signpage = false;
        $signImage = false;
        $html = $this->renderView('AppBundle:default:previewiframeempty.html.twig', array(
            'content' => $content, 'firstpage' => $firstpage, 'signpage' => $signpage, 'signImage' => $signImage));
        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Fetch user list
     * 
     */
    public function clientAction(Request $request, $page) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $result = false;
        $searchTerm = false;
        $limit = 20;
        $offset = ($limit * $page) - $limit;
        $countAll = $em->getRepository('AppBundle:Client')
                ->countAll();

        $clientlist = $em->getRepository('AppBundle:Client')
                ->findByLimitOffset($offset, $limit);
        $pager = new Paginator($page, $countAll, $limit);

        $search = [];
        $form = $this->createFormBuilder($search)
                ->add('search', 'text', array('attr' => array(
                        'placeholder' => 'Name or email',
            )))
                ->add('save', 'submit', array('label' => 'Search'))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchTerm = $data['search'];
            $result = $this->searchClient($searchTerm);
            $clientlist = false;
            $pager = false;
            return $this->render('AppBundle:default:client.html.twig', array(
                        'searchTerm' => $searchTerm, 'result' => $result, 'form' => $form->createView(), 'clientlist' => $clientlist, 'pager' => $pager, 'name' => $name,));
        }

        return $this->render('AppBundle:default:client.html.twig', array(
                    'searchTerm' => $searchTerm, 'result' => $result, 'form' => $form->createView(), 'clientlist' => $clientlist, 'pager' => $pager, 'name' => $name,));
    }

    /**
     * 
     * @param type $searcTerm
     * @return $result unique array
     */
    public function searchClient($searcTerm) {
        $em = $this->getDoctrine()->getManager();
        $nameSearch = $em->getRepository('AppBundle:Client')
                ->searchByName($searcTerm);
        $emailSearch = $em->getRepository('AppBundle:Client')
                ->searchByEmail($searcTerm);
        $result = array_merge($nameSearch, $emailSearch);
        $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
        if (empty($result)) {
            $result = 'no result';
        }
        return $result;
    }

    /**
     * Add new client
     * 
     */
    public function addclientAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();

        $client = new Client();
        $form = $this->createForm(new ClientType(), $client);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $tokenRaw = bin2hex(openssl_random_pseudo_bytes(32));
            $time = time();
            $token = $tokenRaw . '' . $time;
            $client->setUsername($name);
            $firstName = $form->get('firstname')->getData();
            $lastName = $form->get('lastname')->getData();

            $client->setName($firstName . ' ' . $lastName);
            $client->setToken($token);

            $em->persist($client);

            $em->flush();
            return $this->redirectToRoute('app_client');
        }

        return $this->render('AppBundle:default:addclient.html.twig', array(
                    'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Edit existing client
     * 
     */
    public function editclientAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $client = $em->getRepository('AppBundle:Client')
                ->find($id);
        //$client = new Client();
        $form = $this->createForm(new ClientType(), $client);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $tokenRaw = bin2hex(openssl_random_pseudo_bytes(32));
            $time = time();
            $token = $tokenRaw . '' . $time;
            $client->setUsername($name);
            $firstName = $form->get('firstname')->getData();
            $lastName = $form->get('lastname')->getData();

            $client->setName($firstName . ' ' . $lastName);
            $client->setToken($token);

            $em->persist($client);

            $em->flush();
            return $this->redirectToRoute('app_client');
        }

        return $this->render('AppBundle:default:addclient.html.twig', array(
                    'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Send new econtract to a previously saved client
     * 
     */
    public function sendEcontractAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $settingsarray = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $etemplates = $em->getRepository('AppBundle:Etemplate')
                ->findAll();
        $settings = $em->getRepository('AppBundle:Settings')
                ->findAll();
        $emailtemplates = $em->getRepository('AppBundle:Emailtemplate')
                ->findAll();
        //remove default email template
        //var_dump($emailtemplates);die;
        foreach ($emailtemplates as $key => $emailtemplate) {
            if ($emailtemplate->getTempname() === 'default') {
                $defaultKey = $key;
            }
        }
        unset($emailtemplates[$defaultKey]);

        foreach ($settings as $sett) {
            $settingsarray[$sett->getId()] = $sett->getFromname() . ' - ' . $sett->getEusername();
        }

        $reference = [];
        $form = $this->createFormBuilder($reference)
                ->add('tempId', 'hidden', array('read_only' => true))
                ->add('clientId', 'hidden', array('read_only' => true))
                ->add('token', 'hidden', array('read_only' => true))
                ->add('reference', 'text', array('read_only' => true, 'label' => 'Reference', 'required' => true,))
                ->add('email', 'text', array('read_only' => true, 'label' => 'Email'))
                ->add('subject', 'text', array('read_only' => true, 'label' => 'Subject', 'required' => true,))
                ->add('body', 'textarea', array('attr' => array('class' => 'ckeditor')))
                ->add('settings', 'choice', array(
                    'label' => 'From',
                    'choices' => $settingsarray))
                ->add('save', 'submit', array('label' => 'Send contract', 'attr' => array('class' => 'btn btn-danger btn-lg')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $templateID = $form->get('tempId')->getData();
            $clientID = $form->get('clientId')->getData();
            $reference = $form->get('reference')->getData();
            $settid = $form->get('settings')->getData();
            $subject = $form->get('subject')->getData();
            $toemailRaw = $form->get('email')->getData();
            $messageBody = $form->get('body')->getData();
            $token = $form->get('token')->getData();
            $template = $em->getRepository('AppBundle:Etemplate')
                    ->find($templateID);
            $setting = $em->getRepository('AppBundle:Settings')
                    ->find($settid);
            $client = $em->getRepository('AppBundle:Client')
                    ->find($clientID);
            $clientName = $client->getName();
            $trimmedToEmail = strtolower(trim($toemailRaw));
            $toEmail = str_replace(array("\r", "\n"), '', $trimmedToEmail);


            $addressFirstLine = $client->getAddressfirstline();
            $addressTown = $client->getAddresstown();
            $postcode = $client->getPostcode();
            $today = date("d/F/Y");
            $signPage = $template->getSignpage();

            $this->sendEmail($client, $setting, $toEmail, $messageBody, $subject);

            //create the eContract entity
            $eContract = new Econtract();
            $eContract->setContent($template->getContent());
            $eContract->setHeading($template->getHeading());
            $eContract->setFooter($template->getFooter());
            $eContract->setFirstpage($template->getFirstpage());

            //replace placeholders in the last page as name and address
            $signPage = str_replace('%%clientname%%', $clientName, $signPage);
            $signPage = str_replace('%%firstlineaddress%%', $addressFirstLine, $signPage);
            $signPage = str_replace('%%addresstown%%', $addressTown, $signPage);
            $signPage = str_replace('%%postcode%%', $postcode, $signPage);
            $signPage = str_replace('%%date%%', $today, $signPage);
            $eContract->setSignpage($signPage);

            $eContract->setSignature($template->getSignature());
            $eContract->setAddressfirstline($client->getAddressfirstline());
            $eContract->setAddresstown($client->getAddresstown());
            $eContract->setPostcode($client->getPostcode());
            $eContract->setReference($reference);
            $eContract->setTokenactive(true);
            $eContract->setPatientSigned(false);
            $eContract->setEmail($toEmail);
            $eContract->setSettid($settid);
            $eContract->setTokenDate(new \DateTime());
            $expiryObject = new \DateTime();
            $expiryObject->modify('+1 month');
            $eContract->setTokenExpiry($expiryObject);
            $eContract->setUsername($name);
            $eContract->setToken($token);

            $eContract->setClient($client);
            $em->persist($client);
            $em->persist($eContract);
            $em->flush();

            return $this->redirectToRoute('app_sentcontracts');
        }

        return $this->render('AppBundle:default:sendecontract.html.twig', array(
                    'emailtemplates' => $emailtemplates, 'settings' => $settings, 'etemplates' => $etemplates, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * Resend saved econtract to a previously saved client. Link will be regenerated, link expiry reset.
     * 
     */
    public function reSendEcontractAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $settingsarray = [];
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $etemplates = $em->getRepository('AppBundle:Etemplate')
                ->findAll();
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->find($id);
        //var_dump($eContract);die;
        $settings = $em->getRepository('AppBundle:Settings')
                ->findAll();
        $emailtemplates = $em->getRepository('AppBundle:Emailtemplate')
                ->findAll();
        foreach ($settings as $sett) {
            $settingsarray[$sett->getId()] = $sett->getFromname() . ' - ' . $sett->getEusername();
        }

        $reference = [];
        $form = $this->createFormBuilder($reference)
                ->add('reference', 'text', array('data' => $eContract->getReference(), 'read_only' => true, 'label' => 'Reference'))
                ->add('email', 'text', array('data' => $eContract->getClient()->getEmail(), 'label' => 'Email'))
                ->add('subject', 'text', array('read_only' => true, 'label' => 'Subject'))
                ->add('body', 'textarea', array('attr' => array('class' => 'ckeditor')))
                ->add('token', 'hidden', array('read_only' => true))
                ->add('settings', 'choice', array(
                    'label' => 'From',
                    'choices' => $settingsarray))
                ->add('save', 'submit', array('label' => 'Send contract', 'attr' => array('class' => 'btn btn-danger btn-lg')))
                ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $settid = $form->get('settings')->getData();
            $subject = $form->get('subject')->getData();
            $toemailRaw = $form->get('email')->getData();
            $messageBody = $form->get('body')->getData();
            $token = $form->get('token')->getData();
            $setting = $em->getRepository('AppBundle:Settings')
                    ->find($settid);
            $client = $eContract->getClient();
            $trimmedToEmail = strtolower(trim($toemailRaw));
            $toEmail = str_replace(array("\r", "\n"), '', $trimmedToEmail);

            $this->sendEmail($client, $setting, $toEmail, $messageBody, $subject);

            //update eContract object
            $eContract->setTokenDate(new \DateTime());
            $eContract->setEmail($toEmail);
            $eContract->setClientSignature('');
            $eContract->setToken($token);
            $eContract->setTokenactive(1);
            $eContract->setSettid($settid);
            $eContract->setPatientSigned(0);
            $expiryObject = new \DateTime();
            $expiryObject->modify('+1 month');
            $eContract->setTokenExpiry($expiryObject);
            $em->persist($eContract);
            $em->flush();

            return $this->redirectToRoute('app_sentcontracts');
        }

        return $this->render('AppBundle:default:resendecontract.html.twig', array(
                    'eContract' => $eContract, 'emailtemplates' => $emailtemplates, 'settings' => $settings, 'etemplates' => $etemplates, 'form' => $form->createView(), 'name' => $name,));
    }

    /**
     * 
     * @param type $client object
     * @param type $setting object
     * @param type $toEmail
     * @param type $messageBody
     * @param type $subject
     * @return boolean
     */
    public function sendEmail($client, $setting, $toEmail, $messageBody, $subject) {

        $clientName = $client->getName();
        $fromname = $setting->getFromname();

        $smtp = $setting->getSmtp();
        $port = $setting->getPort();
        $mssl = $setting->getEssl();
        $euser = $setting->getEusername();
        $epass = $setting->getEpassword();
        $auth = $setting->getAuth();

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
                ->setTo(array($toEmail => $clientName))
                ->setBody($messageBody, 'text/html')
        ;
        //send the email
        $mailer->getTransport()->start();
        $mailer->send($message);
        $mailer->getTransport()->stop();

        return true;
    }

    /**
     * Receives the email and client IDs, and returns the email body and subject object.
     * Also returns the link for the cumstomer access.
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxappendemailAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $emailId = $request->request->get('emailId');
        //generate token
        $tokenRaw = bin2hex(openssl_random_pseudo_bytes(32)) . '' . time();
        $todayObj = new \DateTime();
        $todayDate = $todayObj->format('d-m-Y');

        //URL NEEDS TO BE CHANGE FOR PRODUCTION ENVIROMENT!!!
        $url = 'http://localhost/esignature/web/app_dev.php/customer';
        $token = $url . '/' . $tokenRaw;
        $mylink = '<a href="' . $token . '">click here to access your contract</a>';

        $myEmailtemplate = $em->getRepository('AppBundle:Emailtemplate')
                ->find($emailId);
        $bodyRaw = $myEmailtemplate->getBody();
        $body = str_replace('%link%', $mylink, $bodyRaw);
        $body = str_replace('%date%', $todayDate, $body);
        $subject = $myEmailtemplate->getSubject();

        $myJson = new JsonResponse(array('body' => $body, 'subject' => $subject, 'token' => $tokenRaw));
        return $myJson;
    }

    /**
     * Toggle Tokenactive boolean property for eContract object
     */
    public function ajaxTokenDisableAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $contractId = $request->request->get('myid');
        $eContract = $em->getRepository('AppBundle:Econtract')
                ->find($contractId);
        if ($eContract) {
            $status = $eContract->getTokenactive();
            if ($status === true) {
                $eContract->setTokenactive(0);
                $em->persist($eContract);
                $em->flush();
            } else {
                $eContract->setTokenactive(1);
                $em->persist($eContract);
                $em->flush();
            }
            $html = 'Success:' . $contractId;
            $response = new Response(json_encode($html));
            return $response;
        }

        $html = 'No result:' . $contractId;
        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Displays sent Econtracts
     * @return list of sent econtracts
     */
    public function sentContractsAction(Request $request, $page) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $result = false;
        $searchTerm = false;

        $em = $this->getDoctrine()->getManager();
        $limit = 10;
        $offset = ($limit * $page) - $limit;
        $countAll = $em->getRepository('AppBundle:Econtract')
                ->countAll();

        //fetch templates with limit and offset
        $econtracts = $em->getRepository('AppBundle:Econtract')
                ->findByLimitOffset($offset, $limit);
        //var_dump($econtracts[0]->getClient()->getName());die;

        $pager = new Paginator($page, $countAll, $limit);

        $search = [];
        $form = $this->createFormBuilder($search)
                ->add('search', 'text', array('attr' => array(
                        'placeholder' => 'Reference or Name',
            )))
                ->add('save', 'submit', array('label' => 'Search'))
                ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $searchTerm = $data['search'];
            $result = $this->searchEcontract($searchTerm);
            $econtracts = false;
            $pager = false;
            return $this->render('AppBundle:default:sentecontracts.html.twig', array(
                        'searchTerm' => $searchTerm, 'result' => $result, 'form' => $form->createView(), 'econtracts' => $econtracts, 'pager' => $pager, 'name' => $name,));
        }

        return $this->render('AppBundle:default:sentecontracts.html.twig', array(
                    'searchTerm' => $searchTerm, 'result' => $result, 'form' => $form->createView(), 'econtracts' => $econtracts, 'pager' => $pager, 'name' => $name,));
    }

    /**
     * @param $searchTerm
     * @return array of eContract objects
     */
    public function searchEcontract($searchTerm) {
        $em = $this->getDoctrine()->getManager();
        $referenceSearch = $em->getRepository('AppBundle:Econtract')
                ->searchByReference($searchTerm);
        $nameSearch = $em->getRepository('AppBundle:Econtract')
                ->searchByName($searchTerm);
        $result = array_merge($referenceSearch, $nameSearch);
        $result = array_map("unserialize", array_unique(array_map("serialize", $result)));
        if (empty($result)) {
            $result = 'no result';
        }
        //var_dump($result);die;
        return $result;
    }

    /**
     * Create self signature and displays saved pictures in a table
     * Only admin can access
     * 
     */
    public function selfSignatureAction(Request $request, $page) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $uploadedImage = false;

        $limit = 10;
        $offset = ($limit * $page) - $limit;
        $countAll = $em->getRepository('AppBundle:Selfsignature')
                ->countAll();

        $selfSignature = $em->getRepository('AppBundle:Selfsignature')
                ->findByLimitOffset($offset, $limit);

        $pager = new Paginator($page, $countAll, $limit);

        $document = new Product();
        $form = $this->createFormBuilder($document)
                ->add('imageFile', 'file', array(
                    'label' => 'Jpg file',))
                ->add('save', 'submit', array('label' => 'Upload', 'attr' => array(
                        'class' => 'btn btn-default btn-sm')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($document);
            $em->flush();
            $imageName = $document->getImageName();

            return $this->render('AppBundle:default:selfsignature.html.twig', array(
                        'uploadedImage' => $imageName, 'form' => $form->createView(), 'selfSignature' => $selfSignature, 'pager' => $pager, 'name' => $name,));
        }
        return $this->render('AppBundle:default:selfsignature.html.twig', array(
                    'uploadedImage' => $uploadedImage, 'form' => $form->createView(), 'selfSignature' => $selfSignature, 'pager' => $pager, 'name' => $name,));
    }

    /**
     * Activates a signature to save 1 to usethis field on the databas.
     * Deactivates the previous active sugnature
     * * 
     */
    public function selfSignUseThisAction($id) {

        $em = $this->getDoctrine()->getManager();

        $findActive = $em->getRepository('AppBundle:Selfsignature')
                ->findOneBy(array('usethis' => 1));
        if ($findActive) {
            $findActive->setUsethis(0);
            $em->persist($findActive);
        }
        $selfSignature = $em->getRepository('AppBundle:Selfsignature')
                ->find($id);
        $selfSignature->setUsethis(1);
        $em->persist($selfSignature);
        $em->flush();
        return $this->redirectToRoute('app_selfSignature');
    }

    /**
     * Persist drawing to database
     * 
     */
    public function ajaxsketchAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $data = $request->request->get('image');
        $signname = $request->request->get('signname');
        $position = $request->request->get('position');
        $company = $request->request->get('company');
        $address = $request->request->get('address');
        $town = $request->request->get('town');
        $postcode = $request->request->get('postcode');


        if ($data) {
            $selfSignature = new Selfsignature();
            $selfSignature->setUsername($name);
            $selfSignature->setImage($data);
            $selfSignature->setSignname($signname);
            $selfSignature->setPosition($position);
            $selfSignature->setCompanyname($company);
            $selfSignature->setAddressfirstline($address);
            $selfSignature->setPostcode($postcode);
            $selfSignature->setAddresstown($town);
            $em->persist($selfSignature);
            $em->flush();

            $html = 'success';
            $response = new Response(json_encode($html));
            return $response;
        }



        $html = 'no data';
        $response = new Response(json_encode($html));
        return $response;
    }

    public function deletesignAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $name = $user->getUsername();
        $signTodelete = $em->getRepository('AppBundle:Selfsignature')
                ->find($id);
        if ($name === $signTodelete->getUsername()) {
            $em->remove($signTodelete);
            $em->flush();
        }
        return $this->redirectToRoute('app_selfSignature');
    }

    public function autocompleteAction() {
        $em = $this->getDoctrine()->getManager();
        $autocomplete = $em->getRepository('AppBundle:Client')
                ->autocomplete();
        $arraylength = count($autocomplete);
        for ($i = 0; $i < $arraylength; $i++) {
            $autocomplete[$i]['value'] = $autocomplete[$i]['name'];
        }
        $response = new Response(json_encode($autocomplete));
        return $response;
    }

    public function emailsettingsAction(request $request) {

        $login = $this->getUser();
        $name = $login->getUsername();
        $em = $this->getDoctrine()->getManager();
        $error = false;
        $settlist = $em->getRepository('AppBundle:Settings')->findAll();
        $settings = new Settings();
        $form = $this->createForm(new SettingsType(), $settings);
        $form2 = $this->createForm(new SettingssmtpType(), $settings);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $settNameRaw = $data->getSettname();
            $settName = trim($settNameRaw);
            $settings->setCreatedAt(new \DateTime());
            //persit data to database
            $settings->setActive(true);
            $settings->setAuth(true);
            $settings->setUsername($name);
            $em->persist($settings);
            $em->flush();
            return $this->redirectToRoute('app_emailsettings');
        }

        $form2->handleRequest($request);

        if ($form2->isValid()) {

            $data = $form->getData();
            //var_dump($data);die;
            $settNameRaw = $data->getSettname();
            $settName = trim($settNameRaw);

            //persit data to database
            $settings->setCreatedAt(new \DateTime());
            $settings->setActive(true);
            $settings->setUsername($name);
            $em->persist($settings);
            $em->flush();
            return $this->redirectToRoute('app_emailsettings');
        }

        return $this->render('AppBundle:Default:settings.html.twig', array(
                    'error' => $error, 'form' => $form->createView(), 'form2' => $form2->createView(), 'name' => $name, 'settlist' => $settlist,
        ));
    }

    /**
     * Activate email settings with id
     * @param type $id
     */
    public function activateemailsettingsAction($id) {
        $em = $this->getDoctrine()->getManager();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $login = $this->getUser();
        if (!in_array('ROLE_ADMIN', $login->getRoles())) {
            return $this->redirectToRoute('app_emailsettings');
        }
        $mySettings = $em->getRepository('AppBundle:Settings')
                ->find($id);

        $isActive = $mySettings->getActive();
        if ($isActive === true) {
            $mySettings->setActive(false);
        } else {
            $mySettings->setActive(true);
        }

        $em->flush();
        return $this->redirectToRoute('app_emailsettings');
    }

    /**
     * Delete email settings with id
     * @param type $id
     */
    public function deleteemailsettingsAction($id) {
        $em = $this->getDoctrine()->getManager();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $login = $this->getUser();
        if (!in_array('ROLE_ADMIN', $login->getRoles())) {
            return $this->redirectToRoute('app_emailsettings');
        }

        $mySettings = $em->getRepository('AppBundle:Settings')
                ->find($id);
        $em->remove($mySettings);
        $em->flush();
        return $this->redirectToRoute('app_emailsettings');
    }

    /**
     * Create and display email templates
     * @param Request $request
     * @return type
     */
    public function emailTemplateAction(Request $request) {
        $login = $this->getUser();
        $name = $login->getUsername();
        $em = $this->getDoctrine()->getManager();
        $emailTemplist = $em->getRepository('AppBundle:Emailtemplate')->findAll();
        if (!$emailTemplist) {
            $emailTemplist = false;
        }
        $confirmationEmail = $em->getRepository('AppBundle:Emailtemplate')
                ->findOneby(array('tempname' => 'default'));
        if (!$confirmationEmail) {
            $confirmationEmail = new Emailtemplate();
            $confirmationEmail->setTempname('default');
            $confirmationEmail->setSubject('Your signed contract attached');
            $confirmationEmail->setBody('<p>Dear %name%,</p><br><br><br><p>Your signed contract is attached.<br/>If you have any question please contact our customer service.</p><p>&nbsp;</p><br><br><p>Kind regards:</p><p>%username%</p><p>&nbsp;</p>');
            $confirmationEmail->setUsername('default');
            $confirmationEmail->setCreatedAt(new \DateTime());
            $em->persist($confirmationEmail);
            $em->flush();
            $emailTemplist = $em->getRepository('AppBundle:Emailtemplate')->findAll();
            if (!$emailTemplist) {
                $emailTemplist = false;
            }
        }

        $emailtemplate = new Emailtemplate();
        $form = $this->createFormBuilder($emailtemplate)
                ->add('tempname', 'text', array(
                    'label' => 'Template name',))
                ->add('subject')
                ->add('body', 'textarea')
                ->add('save', 'submit')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {


            $emailtemplate->setCreatedAt(new \DateTime());
            $emailtemplate->setUsername($name);
            $em->persist($emailtemplate);
            $em->flush();

            return $this->redirectToRoute('app_emailTemplate');
        }
        return $this->render('AppBundle:Default:emailtemplate.html.twig', array('emailTemplist' => $emailTemplist,
                    'form' => $form->createView(), 'name' => $name,
        ));
    }

    /**
     * Edit email templates
     * @param Request $request
     * @return type
     */
    public function editEmailTemplateAction(Request $request, $id) {
        $login = $this->getUser();
        $name = $login->getUsername();
        $em = $this->getDoctrine()->getManager();
        $emailtemplate = $em->getRepository('AppBundle:Emailtemplate')->find($id);
        if (!$emailtemplate) {
            return $this->redirectToRoute('app_emailTemplate');
        }
        $form = $this->createFormBuilder($emailtemplate)
                ->add('tempname', 'text', array(
                    'label' => 'Template name',))
                ->add('subject')
                ->add('body', 'textarea')
                ->add('save', 'submit')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $emailtemplate->setCreatedAt(new \DateTime());
            $emailtemplate->setUsername($name);
            $em->persist($emailtemplate);
            $em->flush();

            return $this->redirectToRoute('app_emailTemplate');
        }
        return $this->render('AppBundle:Default:editemailtemplate.html.twig', array(
                    'form' => $form->createView(), 'name' => $name,
        ));
    }

    /**
     * Delete email settings with id
     * @param type $id
     */
    public function deleteEmailTemplateAction($id) {
        $em = $this->getDoctrine()->getManager();
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }
        $login = $this->getUser();
        if (!in_array('ROLE_ADMIN', $login->getRoles())) {
            return $this->redirectToRoute('app_emailTemplate');
        }

        $myTemplate = $em->getRepository('AppBundle:Emailtemplate')
                ->find($id);
        $em->remove($myTemplate);
        $em->flush();
        return $this->redirectToRoute('app_emailTemplate');
    }

    /**
     * Ajax request to get email template body
     * @param type request
     */
    public function ajaxemailTemplateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->request->get('emailID');
        $myTemplate = $em->getRepository('AppBundle:Emailtemplate')
                ->find($data);

        if ($myTemplate) {

            $html = $myTemplate->getBody();
            $response = new Response(json_encode($html));
            return $response;
        }

        $html = 'no data';
        $response = new Response(json_encode($html));
        return $response;
    }

    /**
     * Delete email settings with id
     * @param type $id
     */
    public function openclientAction($id) {
        $em = $this->getDoctrine()->getManager();
        $login = $this->getUser();
        $name = $login->getUsername();
        $thisClient = $em->getRepository('AppBundle:Client')
                ->find($id);

        return $this->render('AppBundle:Default:openclient.html.twig', array(
                    'client' => $thisClient, 'name' => $name,
        ));
    }

}
