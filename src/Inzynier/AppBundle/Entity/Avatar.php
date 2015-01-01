<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="avatars")
 */
class Avatar {
    private $temp_path;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uploaded;
    
    /**
     * @Assert\File(maxSize=6000000)
     */
    private $file;
    
    public function getUploadDir() {
        return 'images/avatars';
    }
    
    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
    
    public function getWebPath() {
        return null === $this->path ? null : '/' . $this->getUploadDir() . '/' . $this->path;
    }
    
    public function getAbsolutePath() {
        return $this->path === null ? null : $this->getUploadRootDir() . '/' . $this->path;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getFile() {
        return $this->file;
    }
    
    public function setFile(UploadedFile $file) {
        if(isset($this->path)) {
            $this->temp_path = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
        
        $this->file = $file;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if(null !== $this->getFile()) {
            //generating random avatar image name
            $uid = uniqid();
            $extension = $this->getFile()->guessExtension();
            
            $filename = $uid . '.' . $extension;
            $this->path = $filename;
        }
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if($this->file === null) {
            return;
        }
        
        $this->getFile()->move(
                $this->getUploadRootDir(),
                $this->path
        );
        
        if(isset($this->temp_path)) {
            unlink($this->getUploadRootDir() . '/' . $this->temp_path);
            $this->temp_path = null;
        }
        
        $this->file = null;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function remove() {
        $file = $this->getAbsolutePath();
        
        if($file) {
            unlink($file);
        }
    }
}