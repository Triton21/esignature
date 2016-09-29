<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EcontractType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('reference', 'text', array(
                    'label' => 'Reference',
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
            'data_class' => 'AppBundle\Entity\Econtract'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_econtract';
    }

}
