<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class GalleryType extends AbstractType {
    public function getName() {
        return 'gallery';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Entity\Gallery',
        ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('image', 'file');
    }
}