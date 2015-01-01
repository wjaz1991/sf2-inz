<?php

namespace Inzynier\AppBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Inzynier\AppBundle\Form\Type\AuctionAddressType;

class AuctionType extends AbstractType {
    public function getName() {
        return 'auction';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Inzynier\AppBundle\Entity\Auction',
            ));
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', 'text')
                ->add('description', 'textarea')
                ->add('private', 'checkbox')
                ->add('startDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ))
                ->add('endDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ))
                ->add('price', 'number')
                ->add('category', 'entity', array(
                    'class' => 'InzynierAppBundle:AuctionCategory',
                    'property' => 'name',
                ))
                ->add('images', 'collection', array(
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type' => new GalleryType(),
                    'label' => false,
                    'by_reference' => true,
                ))
                ->add('address', new AuctionAddressType());
    }
}