<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AuctionAddressType extends AbstractType {
    public function getName() {
        return 'auction_address';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Inzynier\AppBundle\Entity\AuctionAddress',
            'label' => false,
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('street', 'text', [
            'label' => 'form.auction_address.street'
        ])
                ->add('postcode', 'text', [
            'label' => 'form.auction_address.postcode'
        ])
                ->add('city', 'text', [
            'label' => 'form.auction_address.city'
        ])
                ->add('country', 'text', [
            'label' => 'form.auction_address.country'
        ]);
    }
}

