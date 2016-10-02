<?php

namespace AppBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emailtemplate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EmailtemplateRepository")
 * @ORM\HasLifecycleCallbacks() 
 */
class Emailtemplate {

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
     * @ORM\Column(name="tempname", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\NotEqualTo("default")
     */
    private $tempname;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=21000)
     * @Assert\NotBlank()
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

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
     * Set tempname
     *
     * @param string $tempname
     *
     * @return Emailtemplate
     */
    public function setTempname($tempname)
    {
        $this->tempname = $tempname;

        return $this;
    }

    /**
     * Get tempname
     *
     * @return string
     */
    public function getTempname()
    {
        return $this->tempname;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Emailtemplate
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Emailtemplate
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Emailtemplate
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Emailtemplate
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
}
