<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class AddressType extends AbstractType {
    public function getName() {
        return 'address';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults([
            'data_class' => 'Inzynier\AppBundle\Entity\Address',
            'label' => false,
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('street', 'text', [
            'label' => 'form.address.street',
        ])
                ->add('postcode', 'text', [
                    'label' => 'form.address.postcode',
                ])
                ->add('telephone', 'text', [
                    'label' => 'form.address.telephone',
                ])
                ->add('city', 'text', [
                    'label' => 'form.address.city',
                ])
                ->add('country', 'text', [
                    'label' => 'form.address.country',
                ]);
    }
}

