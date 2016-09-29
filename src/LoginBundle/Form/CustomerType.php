<?php

namespace LoginBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'label' => 'Username',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter Username'
                )
            ))
            ->add('firstname', 'text', array(
                'label' => 'First Name',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter First Name'
                )
            ))
            ->add('lastname', 'text', array(
                'label' => 'Last Name',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter Last Name'
                )
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter email'
                )
            ))
            ->add('password', 'password', array(
                'label' => 'Password',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Enter password'
                )))
            ->add('isAdmin', 'checkbox', array(
                'label' => 'Make admin',
                'required' => false,
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LoginBundle\Entity\Customer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'loginbundle_customer';
    }
}
