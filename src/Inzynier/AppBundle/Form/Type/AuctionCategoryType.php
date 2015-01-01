<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AuctionCategoryType extends AbstractType {
    public function getName() {
        return 'auction_category';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Inzynier\AppBundle\Entity\AuctionCategory',
            'label' => false,
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', 'text')
                ->add('parent', 'entity', [
                    'class' => 'InzynierAppBundle:AuctionCategory',
                    'property' => 'name',
                    'empty_value' => 'Select category',
                ]);
    }
}