<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etemplate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EtemplateRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Etemplate {

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
     */
    private $tempname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;
    
    /**
     * @var string
     *
     * @ORM\Column(name="heading", type="text")
     */
    private $heading;
    
    /**
     * @var string
     *
     * @ORM\Column(name="footer", type="text")
     */
    private $footer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstpage", type="text")
     */
    private $firstpage;
    
    /**
     * @var string
     *
     * @ORM\Column(name="signpage", type="text", nullable=true)
     */
    private $signpage;
    
    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="text", nullable=true)
     */
    private $signature;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set tempname
     *
     * @param string $tempname
     *
     * @return Etemplate
     */
    public function setTempname($tempname) {
        $this->tempname = $tempname;

        return $this;
    }

    /**
     * Get tempname
     *
     * @return string
     */
    public function getTempname() {
        return $this->tempname;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Etemplate
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }


    /**
     * Set username
     *
     * @param string $username
     *
     * @return Etemplate
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
     * @return Etemplate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set heading
     *
     * @param string $heading
     *
     * @return Etemplate
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get heading
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set footer
     *
     * @param string $footer
     *
     * @return Etemplate
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Get footer
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set firstpage
     *
     * @param string $firstpage
     *
     * @return Etemplate
     */
    public function setFirstpage($firstpage)
    {
        $this->firstpage = $firstpage;

        return $this;
    }

    /**
     * Get firstpage
     *
     * @return string
     */
    public function getFirstpage()
    {
        return $this->firstpage;
    }

    /**
     * Set signpage
     *
     * @param string $signpage
     *
     * @return Etemplate
     */
    public function setSignpage($signpage)
    {
        $this->signpage = $signpage;

        return $this;
    }

    /**
     * Get signpage
     *
     * @return string
     */
    public function getSignpage()
    {
        return $this->signpage;
    }

    /**
     * Set signature
     *
     * @param string $signature
     *
     * @return Etemplate
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }
}
