<?php

namespace Inzynier\AppBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;

class PathLocator {
    private $request;
    
    public function __construct(RequestStack $request) {
        $this->request = $request->getCurrentRequest();
    }
    
    public function getImagesUploadDir() {
        return __DIR__ . '/../../../../web/images';
    }
    
    public function getWebImagesUploadDir() {
        return 'http://' . $this->request->server->get('HTTP_HOST')
                . '/web/images/';
    }
    
    public function getAssetsPath() {
        return __DIR__ . '/../../../../web/';
    }
    
    public function getWebAssetsPath() {
        return 'http://' . $this->request->server->get('HTTP_HOST')
                . '/web/';
    }
}

