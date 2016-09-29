<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ClientRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Client
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Econtract", mappedBy="client")
     */
    private $econtracts;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text")
     */
    private $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="addressfirstline", type="text")
     */
    private $addressfirstline;
    
    /**
     * @var string
     *
     * @ORM\Column(name="addresstown", type="text")
     */
    private $addresstown;
    
    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="text")
     */
    private $postcode;
    
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="text")
     */
    private $username;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text", nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="dob", type="datetime")
     */
    private $dob;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Client
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Client
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Client
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add econtract
     *
     * @param \AppBundle\Entity\Econtract $econtract
     *
     * @return Client
     */
    public function addEcontract(\AppBundle\Entity\Econtract $econtract)
    {
        $this->econtracts[] = $econtract;

        return $this;
    }

    /**
     * Remove econtract
     *
     * @param \AppBundle\Entity\Econtract $econtract
     */
    public function removeEcontract(\AppBundle\Entity\Econtract $econtract)
    {
        $this->econtracts->removeElement($econtract);
    }

    /**
     * Get econtracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEcontracts()
    {
        return $this->econtracts;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Client
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set dob
     *
     * @param \DateTime $dob
     *
     * @return Client
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     *
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Client
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Client
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set addressfirstline
     *
     * @param string $addressfirstline
     *
     * @return Client
     */
    public function setAddressfirstline($addressfirstline)
    {
        $this->addressfirstline = $addressfirstline;

        return $this;
    }

    /**
     * Get addressfirstline
     *
     * @return string
     */
    public function getAddressfirstline()
    {
        return $this->addressfirstline;
    }

    /**
     * Set addresstown
     *
     * @param string $addresstown
     *
     * @return Client
     */
    public function setAddresstown($addresstown)
    {
        $this->addresstown = $addresstown;

        return $this;
    }

    /**
     * Get addresstown
     *
     * @return string
     */
    public function getAddresstown()
    {
        return $this->addresstown;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return Client
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }
}
