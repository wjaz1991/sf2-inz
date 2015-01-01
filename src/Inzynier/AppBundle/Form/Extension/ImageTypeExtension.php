<?php

namespace Inzynier\AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageTypeExtension extends AbstractTypeExtension {
    public function getExtendedType() {
        return 'file';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setOptional(['image_path']);
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        if(array_key_exists('image_path', $options)) {
            $parentData = $form->getParent()->getData();
            
            if($parentData !== null) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $image_url = $accessor->getValue($parentData, $options['image_path']);
            } else {
                $image_url = null;
            }
            
            $view->vars['image_url'] = $image_url;
        }
    }
}