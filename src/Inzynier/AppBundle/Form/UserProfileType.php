<?php

namespace Inzynier\AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Inzynier\AppBundle\Form\Type\AvatarType;

class UserProfileType extends AbstractType {
    public function getName() {
        return 'user_profile';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Entity\User',
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', 'text')
                ->add('email', 'email')
                ->add('avatar', new AvatarType());
    }
}