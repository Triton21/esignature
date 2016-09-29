<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingssmtpType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('settname', 'text', array(
                    'label' => 'Setting name',
                    'data' => 'Smtp settings'))
                ->add('fromname', 'text', array(
                    'label' => 'Sender name',
                ))
                ->add('smtp', 'text', array(
                    'label' => 'Smtp',
                    'data' => 'smtp.example.org'))
                ->add('port', 'text', array(
                    'label' => 'Port',
                    'data' => '25'))
                ->add('essl', 'text', array(
                    'label' => 'ssl',
                    'data' => 'ssl'))
                ->add('auth', 'checkbox', array(
                    'label' => 'Authentication requred?',
                    'required' => false,
                ))
                ->add('eusername', 'text', array(
                    'label' => 'Email username',
                    'data' => 'username@gmail.com'))
                ->add('epassword', 'password', array(
                    'label' => 'Email password',
                    'data' => 'password'))
                ->add('active', 'hidden', array(
                    'data' => '1',
                ))
                ->add('save', 'submit')
        //->add('username')
        //->add('createdat')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Settings'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_settingssmtp';
    }

}
