<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType {
    public function getName() {
        return 'post';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Inzynier\AppBundle\Entity\Post',
            'label' => false,
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('text', 'textarea', [
            'label' => false,
        ]);
    }
}