<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EtemplateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tempname', 'text', array(
                    'label' => 'Template name',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Enter template name'
                    )
                ))
                ->add('heading', 'textarea', array(
                    'label' => 'Content',
                    'required' => true,
                ))
                ->add('firstpage', 'textarea', array(
                    'label' => 'Firstpage',
                    'required' => true,
                ))
                ->add('content', 'textarea', array(
                    'label' => 'Content',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Enter Username',
                    )
                ))
                ->add('footer', 'textarea', array(
                    'label' => 'Footer',
                    'required' => true,
                ))
                ->add('signpage', 'textarea', array(
                    'label' => 'Signature',
                    'required' => true,
                ))
                ->add('save', 'submit', array(
                    'label' => 'Save', 'attr' => array('class' => 'btn btn-default btn-md')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Etemplate',
            'attr' => array(
                'class' => 'form'
            ),
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_etemplate';
    }

}
