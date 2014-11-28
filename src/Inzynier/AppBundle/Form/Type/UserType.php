<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {
    public function getName() {
        return 'user';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Entity\User'
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', 'text', array(
                    'label' => 'Provide an username',
                ))
                ->add('email', 'email', array(
                    'label' => 'Provide an email',
                    'attr' => array(
                        'input_group' => array(
                            'prepend' => '@',
                            'size' => 'medium',
                        )
                    )
                ))
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Password fields must match',
                    'required' => true,
                    'first_options' => array('label' => 'Provide a password'),
                    'second_options' => array('label' => 'Repeat a password')
                ));
    }
}
