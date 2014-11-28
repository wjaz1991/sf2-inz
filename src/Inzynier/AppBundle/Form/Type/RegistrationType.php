<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType {
    public function getName() {
        return 'registration';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Classes\Registration'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('user', new UserType(), array('label' => false))
                ->add('terms', 'checkbox', array(
                    'property_path' => 'termsAccepted',
                    'label' => 'Agree to the terms of service',
                ));
    }
}
