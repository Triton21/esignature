<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('firstname', 'text', array(
                    'label' => 'First name',
                    'required' => true,
                    'attr' => array(
                        'autocompelete' => 'off',
                        'placeholder' => 'Enter first name'
            )))
                ->add('lastname', 'text', array(
                    'label' => 'Last name',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Enter last name'
            )))
                ->add('name', 'text', array(
                    'label' => 'Name',
                    'required' => true,
                    'disabled' => true,
                ))
                ->add('email', 'email', array(
                    'label' => 'Email',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Enter email address'
            )))
                ->add('phone', 'text', array(
                    'label' => 'Phone',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => '07xxxxxxxx'
            )))
                ->add('dob', 'birthday', array(
                    'format' => 'dd MM yyyy',
                    'placeholder' => array(
                        'day' => 'Day', 'month' => 'Month', 'year' => 'Year',
                    )
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
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_client';
    }

}
