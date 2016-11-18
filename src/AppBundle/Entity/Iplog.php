<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Iplog
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\IplogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Iplog
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
     * @ORM\Column(name="ip", type="text")
     */
    private $ip;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text")
     */
    private $token;
    
    /**
     * @var string
     *
     * @ORM\Column(name="wrongdob", type="integer", nullable=true)
     */
    private $wrongdob;

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
     * Set ip
     *
     * @param string $ip
     *
     * @return Iplog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Iplog
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
     * Set token
     *
     * @param string $token
     *
     * @return Iplog
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
     * Set wrongdob
     *
     * @param integer $wrongdob
     *
     * @return Iplog
     */
    public function setWrongdob($wrongdob)
    {
        $this->wrongdob = $wrongdob;

        return $this;
    }

    /**
     * Get wrongdob
     *
     * @return integer
     */
    public function getWrongdob()
    {
        return $this->wrongdob;
    }
}
