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
        $builder->add('title', 'text', [
                    'label' => 'form.auction.title',
                ])
                ->add('description', 'textarea', [
                    'label' => 'form.auction.description',
                ])
                ->add('startDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'form.auction.startDate',
                ))
                ->add('endDate', 'date', array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'form.auction.endDate',
                ))
                ->add('price', 'number', [
                    'label' => 'form.auction.price',
                ])
                ->add('category', 'entity', array(
                    'class' => 'InzynierAppBundle:AuctionCategory',
                    'property' => 'name',
                    'label' => 'form.auction.category',
                ))
                ->add('images', 'collection', array(
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type' => new GalleryType(),
                    'by_reference' => true,
                    'label' => 'form.auction.images',
                ))
                ->add('address', new AuctionAddressType());
    }
}