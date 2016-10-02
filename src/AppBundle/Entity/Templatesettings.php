<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Templatesettings
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\TemplatesettingsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Templatesettings
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
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="heading", type="text", nullable=true)
     */
    private $heading;
    
    /**
     * @var string
     *
     * @ORM\Column(name="signatureid", type="integer", nullable=true)
     */
    private $signatureid;
    
    /**
     * @var string
     *
     * @ORM\Column(name="usethis", type="text", nullable=true)
     */
    private $usethis;
    
    /**
     * @var string
     *
     * @ORM\Column(name="settingsname", type="text")
     */
    private $settingsname;

    /**
     * @var string
     *
     * @ORM\Column(name="footer", type="text", nullable=true)
     */
    private $footer;
    
    /**
     * @var string
     *
     * @ORM\Column(name="firstpage", type="text", nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
    
    /**
     * @var string
     *
     * @ORM\Column(name="companyname", type="text", nullable=true)
     */
    private $companyname;
    
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Templateheading
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
     * Set heading
     *
     * @param string $heading
     *
     * @return Templateheading
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
     * @return Templateheading
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
     * Set username
     *
     * @param string $username
     *
     * @return Templateheading
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
     * Set firstpage
     *
     * @param string $firstpage
     *
     * @return Templateheading
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
     * Set settingsname
     *
     * @param string $settingsname
     *
     * @return Templateheading
     */
    public function setSettingsname($settingsname)
    {
        $this->settingsname = $settingsname;

        return $this;
    }

    /**
     * Get settingsname
     *
     * @return string
     */
    public function getSettingsname()
    {
        return $this->settingsname;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set usethis
     *
     * @param string $usethis
     *
     * @return Templateheading
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
     * Set signpage
     *
     * @param string $signpage
     *
     * @return Templatesettings
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
     * @return Templatesettings
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

    /**
     * Set companyname
     *
     * @param string $companyname
     *
     * @return Templatesettings
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
     * @return Templatesettings
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
     * @return Templatesettings
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
     * @return Templatesettings
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

    /**
     * Set signatureid
     *
     * @param integer $signatureid
     *
     * @return Templatesettings
     */
    public function setSignatureid($signatureid)
    {
        $this->signatureid = $signatureid;

        return $this;
    }

    /**
     * Get signatureid
     *
     * @return integer
     */
    public function getSignatureid()
    {
        return $this->signatureid;
    }
}
