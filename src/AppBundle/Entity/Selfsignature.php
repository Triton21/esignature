<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Selfsignature
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SelfsignatureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Selfsignature
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
    
    /**
     * @var string
     *
     * @ORM\Column(name="signname", type="string", length=255)
     */
    private $signname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\Column(name="companyname", type="string", length=255)
     */
    private $companyname;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text")
     */
    private $image;
    
    /**
     * @var string
     *
     * @ORM\Column(name="usethis", type="text", nullable=true)
     */
    private $usethis;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var string
     *
     * @ORM\Column(name="addressfirstline", type="text", nullable=true)
     */
    private $addressfirstline;
    
    /**
     * @var string
     *
     * @ORM\Column(name="addresstown", type="text", nullable=true)
     */
    private $addresstown;
    
    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="text", nullable=true)
     */
    private $postcode;


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
     * Set username
     *
     * @param string $username
     *
     * @return Selfsignature
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
     * Set image
     *
     * @param string $image
     *
     * @return Selfsignature
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Selfsignature
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set signname
     *
     * @param string $signname
     *
     * @return Selfsignature
     */
    public function setSignname($signname)
    {
        $this->signname = $signname;

        return $this;
    }

    /**
     * Get signname
     *
     * @return string
     */
    public function getSignname()
    {
        return $this->signname;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return Selfsignature
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set usethis
     *
     * @param string $usethis
     *
     * @return Selfsignature
     */
    public function setUsethis($usethis)
    {
        $this->usethis = $usethis;

        return $this;
    }

    /**
     * Get usethis
     *
     * @return string
     */
    public function getUsethis()
    {
        return $this->usethis;
    }

    /**
     * Set companyname
     *
     * @param string $companyname
     *
     * @return Selfsignature
     */
    public function setCompanyname($companyname)
    {
        $this->companyname = $companyname;

        return $this;
    }

    /**
     * Get companyname
     *
     * @return string
     */
    public function getCompanyname()
    {
        return $this->companyname;
    }

    /**
     * Set addressfirstline
     *
     * @param string $addressfirstline
     *
     * @return Selfsignature
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
     * @return Selfsignature
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
     * @return Selfsignature
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
