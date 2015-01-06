<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class BidType extends AbstractType {
    public function getName() {
        return 'bid';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Inzynier\AppBundle\Entity\Bid',
            'label' => false
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('price', 'money', [
            'label' => 'form.bid.price',
        ]);
    }
}

