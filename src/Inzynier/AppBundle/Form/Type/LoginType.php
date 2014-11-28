<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class LoginType extends AbstractType {
    public function getName() {
        return 'login';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Classes\Login',
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('_username', 'text', array(
                    'label' => 'Enter your login',
                     'property_path' => 'login',
                    'attr' => array(
                        'help_text' => 'Can be your email or username.',
                    )
                ))
                ->add('_password', 'password', array(
                    'label' => 'Enter your password',
                    'property_path' => 'password',
                ))
                ->add('remember', 'checkbox', array(
                    'label' => 'Remember me'
                ));
    }
}

