<?php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Settings
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SettingsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Settings {
    
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
     * @ORM\Column(name="smtp", type="string", length=255)
     */
    private $smtp;
    
    /**
     * @var string
     *
     * @ORM\Column(name="port", type="string", length=255)
     */
    private $port;
    
    /**
     * @var string
     *
     * @ORM\Column(name="auth", type="boolean")
     */
    private $auth;
    
    /**
     * @var string
     *
     * @ORM\Column(name="essl", type="string", length=255)
     */
    private $essl;
    
    /**
     * @var string
     *
     * @ORM\Column(name="eusername", type="text", length=255)
     */
    private $eusername;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fromname", type="string", length=255, nullable=true)
     */
    private $fromname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="epassword", type="text", length=255)
     */
    private $epassword;
    
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdat", type="datetime")
     */
    private $createdat;
    
    /**
     * @var string
     *
     * @ORM\Column(name="settname", type="string", length=255)
     */
    private $settname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;
    

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
     * Set smtp
     *
     * @param string $smtp
     *
     * @return Settings
     */
    public function setSmtp($smtp)
    {
        $this->smtp = $smtp;

        return $this;
    }

    /**
     * Get smtp
     *
     * @return string
     */
    public function getSmtp()
    {
        return $this->smtp;
    }

    /**
     * Set port
     *
     * @param string $port
     *
     * @return Settings
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set auth
     *
     * @param string $auth
     *
     * @return Settings
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get auth
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set essl
     *
     * @param string $essl
     *
     * @return Settings
     */
    public function setEssl($essl)
    {
        $this->essl = $essl;

        return $this;
    }

    /**
     * Get essl
     *
     * @return string
     */
    public function getEssl()
    {
        return $this->essl;
    }

    /**
     * Set eusername
     *
     * @param string $eusername
     *
     * @return Settings
     */
    public function setEusername($eusername)
    {
        $this->eusername = $eusername;

        return $this;
    }

    /**
     * Get eusername
     *
     * @return string
     */
    public function getEusername()
    {
        return $this->eusername;
    }

    /**
     * Set fromname
     *
     * @param string $fromname
     *
     * @return Settings
     */
    public function setFromname($fromname)
    {
        $this->fromname = $fromname;

        return $this;
    }

    /**
     * Get fromname
     *
     * @return string
     */
    public function getFromname()
    {
        return $this->fromname;
    }

    /**
     * Set epassword
     *
     * @param string $epassword
     *
     * @return Settings
     */
    public function setEpassword($epassword)
    {
        $this->epassword = $epassword;

        return $this;
    }

    /**
     * Get epassword
     *
     * @return string
     */
    public function getEpassword()
    {
        return $this->epassword;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Settings
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
     * Set createdat
     *
     * @param \DateTime $createdat
     *
     * @return Settings
     */
    public function setCreatedat($createdat)
    {
        $this->createdat = $createdat;

        return $this;
    }

    /**
     * Get createdat
     *
     * @return \DateTime
     */
    public function getCreatedat()
    {
        return $this->createdat;
    }

    /**
     * Set settname
     *
     * @param string $settname
     *
     * @return Settings
     */
    public function setSettname($settname)
    {
        $this->settname = $settname;

        return $this;
    }

    /**
     * Get settname
     *
     * @return string
     */
    public function getSettname()
    {
        return $this->settname;
    }

    /**
     * Set active
     *
     * @param integer $active
     *
     * @return Settings
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }
}
