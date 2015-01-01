<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="galleries")
 * @ORM\HasLifecycleCallbacks
 */
class Gallery {
    private $temp_path;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $filename;
    
    /**
     * @ORM\ManyToOne(targetEntity="Auction", inversedBy="images")
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    protected $auction;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $uploaded;
    
    /**
     *
     * @Assert\File(maxSize=6000000)
     */
    protected $image;
    
    public function __construct() {
        $this->uploaded = true;
    }
    
    public function getUploaded() {
        return $this->uploaded;
    }
    
    public function setUploaded($uploaded) {
        $this->uploaded = $uploaded;
        return $this;
    }
    
    public function getImage() {
        return $this->image;
    }
    
    public function setImage(UploadedFile $image) {
        if(isset($this->filename)) {
            $this->temp_path = $this->filename;
            $this->filename = null;
        } else {
            $this->filename = 'initial';
        }
        
        $this->image = $image;
        
        return $this;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }
    
    public function getFilename() {
        return $this->filename;
    }
    
    public function getAuction() {
        return $this->auction;
    }
    
    public function setAuction(Auction $auction) {
        $this->auction = $auction;
        return $this;
    }
    
    public function getUploadDir() {
        return 'images/galleries';
    }
    
    public function getUploadRootDir() {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }
    
    public function getWebPath() {
        return $this->filename === null ? null : '/' . $this->getUploadDir() . '/' . $this->filename;
    }
    
    public function getAbsolutePath() {
        return $this->filename === null ? null : $this->getUploadRootDir() . '/' . $this->filename;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if($this->getImage() !== null) {
            $name = uniqid();
            $extension = $this->getImage()->guessExtension();
            $filename = $name . '.' . $extension;
            
            $this->filename = $filename;
        }
        dump($this->image);
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        if($this->filename === null) {
            return;
        }
        
        $this->getImage()->move(
                $this->getUploadRootDir(),
                $this->getFilename()
        );
        
        if(isset($this->temp_path)) {
            unlink($this->getUploadRootDir() . '/' . $this->temp_path);
            $temp_path = null;
        }
        
        $this->image = null;
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