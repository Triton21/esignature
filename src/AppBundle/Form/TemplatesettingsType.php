<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TemplatesettingsType extends AbstractType
{
    
    
    private $initHeading;
    private $initFooter;
    private $initSignpage;
    private $signatureArray;
    
    
    function __construct($initHeading, $initFooter, $initSignpage, $signatureArray) {
        $this->initHeading = $initHeading;
        $this->initFooter = $initFooter;
        $this->initSignpage = $initSignpage;
        $this->signatureArray = $signatureArray;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('settingsname', 'text', array(
                    'label' => 'Template name',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Settings name'
                    )
                ))
                ->add('heading', 'textarea', array(
                    'label' => 'Content',
                    'data' => $this->initHeading,
                    'required' => true,
                    
                ))
                ->add('firstpage', 'textarea', array(
                    'label' => 'Firstpage',
                    'required' => true,
                    
                ))
                ->add('footer', 'textarea', array(
                    'label' => 'Footer',
                    'data' => $this->initFooter,
                    'required' => true,
                ))
                ->add('signpage', 'textarea', array(
                    'label' => 'Signature',
                    'data' => $this->initSignpage,
                    'required' => true,
                ))
                ->add('signatureid', 'choice', array(
                    'label' => 'Signature',
                    'choices' => $this->signatureArray, 
                    'attr' => array(
                        'placeholder' => 'Select signature'
                    )))
                ->add('save', 'submit', array(
                    'label' => 'Save', 'attr' => array('class' => 'btn btn-default btn-md')))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Templatesettings'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_templatesettings';
    }
}
