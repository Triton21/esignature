<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyName', 'text', array(
                    'label' => 'Name',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Company name')))
            ->add('address', 'text', array(
                    'label' => 'Address',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Address')))
            ->add('website', 'text', array(
                    'label' => 'Website',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Website')))
            ->add('email', 'text', array(
                    'label' => 'Email',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Email')))
            ->add('phone', 'text', array(
                    'label' => 'Phone',
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'Phone')))
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
            'data_class' => 'AppBundle\Entity\Company'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_company';
    }
}
