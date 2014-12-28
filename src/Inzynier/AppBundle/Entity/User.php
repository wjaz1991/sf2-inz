<?php

namespace Inzynier\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

use Inzynier\AppBundle\Repository\UserRepository;
use Inzynier\AppBundle\Entity\Address;

/**
 * @ORM\Entity(repositoryClass="Inzynier\AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity(fields="email", message="Provided email is in use")
 * @UniqueEntity(fields="username", message="Choosed username is in use")
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    protected $username;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;
    
    /**
     * @ORM\Column(type="text", length=4096)
     * @Assert\NotBlank()
     */
    protected $password;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;
    
    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="user")
     */
    protected $addresses;
    
    /**
     * @ORM\OneToMany(targetEntity="Bid", mappedBy="user")
     */
    protected $bids;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $dateAdded;
    
    /**
     * @ORM\OneToMany(targetEntity="Auction", mappedBy="user")
     */
    protected $auctions;
    
    /**
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="user_one", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $my_friends;
    
    /**
     * @ORM\OneToMany(targetEntity="Friendship", mappedBy="user_two", cascade={"persist", "remove"}, orphanRemoval=TRUE)
     */
    protected $friends_with_me;
    
    /**
     * @ORM\OneToOne(targetEntity="Avatar", cascade={"persist"})
     * @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     */
    private $avatar;
    
    public function __construct() {
        $this->isActive = true;
        $this->addresses = new ArrayCollection;
        $this->avatar = '';
        $this->bids = new ArrayCollection;
        $this->auctions = new ArrayCollection;
        $this->dateAdded = new \DateTime();
        $this->my_friends = new ArrayCollection();
        $this->friends_with_me = new ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getAvatar() {
        return $this->avatar;
    }
    
    public function setAvatar(Avatar $avatar) {
        $this->avatar = $avatar;
        return $this;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setIsActive($active) {
        $this->isActive = $active;
        return $this;
    }
    
    public function getIsActive() {
        return $this->isActive;
    }
    
    public function addBid(Bid $bid) {
        $this->bids[] = $bid;
        return $this;
    }
    
    public function removeBid(Bid $bid) {
        $this->bids->removeElement($bid);
        return $this;
    }
    
    public function getBids() {
        return $this->bids;
    }
    
    public function addAuction(Auction $auction) {
        $this->auctions[] = $auction;
        return $this;
    }
    
    public function removeAuction(Auction $auction) {
        $this->auctions->removeElement($auction);
        return $this;
    }
    
    public function getAuctions() {
        return $this->auctions;
    }
    
    public function getDateAdded() {
        return $this->dateAdded;
    }
    
    public function setDateAdded(\DateTime $date) {
        $this->dateAdded = $date;
        return $this;
    }

    /**
     * Add addresses
     *
     * @param \Inzynier\AppBundle\Entity\Address $address
     * @return User
     */
    public function addAddress(\Inzynier\AppBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;
        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Inzynier\AppBundle\Entity\Address $address
     */
    public function removeAddress(Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
    
    public function getMyFriends() {
        return $this->my_friends;
    }
    
    public function addMyFriend(Friendship $friendship) {
        if(!$this->my_friends->contains($friendship)) {
            $this->my_friends->add($friendship);
            $friendship->setUserOne($this);
        }
        
        return $this;
    }
    
    public function removeMyFriend(Friendship $friendship) {
        if(!$this->my_friends->contains($friendship)) {
            $this->my_friends->remove($friendship);
            $friendship->setUserOne(null);
        }
        
        return $this;
    }
    
    public function getFriendsWithMe() {
        return $this->friends_with_me;
    }
    
    public function addFriendWithMe(Friendship $friendship) {
        if(!$this->friends_with_me->contains($friendship)) {
            $this->friends_with_me->add($friendship);
            $friendship->setUserTwo($this);
        }
        
        return $this;
    }
    
    public function removeFriendWithMe(Friendship $friendship) {
        if(!$this->friends_with_me->contains($friendship)) {
            $this->friends_with_me->remove($friendship);
            $friendship->setUserTwo(null);
        }
        
        return $this;
    }
    
//    public function getMyFriends() {
//        return $this->friends;
//    }
//    
//    public function addMyFriend(User $user) {
//        if(!$this->my_friends->contains($user)) {
//            $this->my_friends->add($user);
//            $user->addFriendWithMe($this);
//        }
//        
//        return $this;
//    }
//    
//    public function removeMyFriend(User $user) {
//        if(!$this->my_friends->contains($user)) {
//            $this->my_friends->remove($user);
//            $user->removeFriendWithMe($this);
//        }
//        
//        return $this;
//    }
//    
//    public function getFriendsWithMe() {
//        return $this->friends_with_me;
//    }
//    
//    public function addFriendWithMe(User $user) {
//        if(!$this->friends_with_me->contains($user)) {
//            $this->friends_with_me->add($user);
//            $user->addMyFriend($user);
//        }
//        
//        return $this;
//    }
//    
//    public function removeFriendWithMe(User $user) {
//        if(!$this->friends_with_me->contains($user)) {
//            $this->friends_with_me->remove($user);
//            $user->removeMyFriend($user);
//        }
//        
//        return $this;
//    }
    
    //implementing interfaces
    public function getSalt() {
        return null;
    }
    
    public function getRoles() {
        return array('ROLE_USER');
    }
    
    public function eraseCredentials() {
    }
    
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }
    
    public function unserialize($serialized) {
        list($this->id, $this->username, $this->password) = 
                unserialize($serialized);
        return $this;
    }
    
    public function isAccountNonExpired() {
        return true;
    }
    
    public function isAccountNonLocked() {
        return true;
    }
    
    public function isCredentialsNonExpired() {
        return true;
    }
    
    public function isEnabled() {
        return $this->isActive;
    }
    
    public function __toString() {
        return 'user';
    }
}
