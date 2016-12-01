<?php

namespace AppBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Econtract
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EcontractRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Econtract
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
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="econtracts")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     */
    private $client;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tokenDate", type="datetime")
     */
    private $tokenDate;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tokenExpiry", type="datetime")
     */
    private $tokenExpiry;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="clientsignDate", type="datetime", nullable=true)
     */
    private $clientsignDate;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text", nullable=true)
     */
    private $filename;
    
    /**
     * @var string
     *
     * @ORM\Column(name="emailContent", type="text", nullable=true)
     */
    private $emailContent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="extraContent", type="text", nullable=true)
     */
    private $extraContent;
    
    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="text")
     */
    private $reference;
    
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
    
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
     * @ORM\Column(name="firstpage", type="text", nullable=true)
     */
    private $firstpage;
    
    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="text", nullable=true)
     */
    private $filepath;
    
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
     * @ORM\Column(name="clientSignature", type="text", nullable=true)
     */
    private $clientSignature;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text")
     */
    private $token;
    
    /**
     * @var string
     *
     * @ORM\Column(name="templateid", type="integer", nullable=true)
     */
    private $templateid;
    
    /**
     * @var string
     *
     * @ORM\Column(name="settid", type="integer", nullable=true)
     */
    private $settid;
    
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
     * @var boolean
     *
     * @ORM\Column(name="tokenactive", type="boolean")
     */
    private $tokenactive;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="patientSigned", type="boolean")
     */
    private $patientSigned;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="needSelfSign", type="boolean", nullable=true)
     */
    private $needSelfSign;
    
    /**
     * @var string
     *
     * @ORM\Column(name="postcode", type="text")
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
     * Set content
     *
     * @param string $content
     *
     * @return Econtract
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Econtract
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
     * Set filename
     *
     * @param string $filename
     *
     * @return Econtract
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Econtract
     */
    public function setClient(\AppBundle\Entity\Client $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Econtract
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Econtract
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
     * Set heading
     *
     * @param string $heading
     *
     * @return Econtract
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
     * @return Econtract
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
     * @return Econtract
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
     * @return Econtract
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
     * @return Econtract
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
     * Set token
     *
     * @param string $token
     *
     * @return Econtract
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
     * @return Econtract
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
     * @return Econtract
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
     * @return Econtract
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue() {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set tokenDate
     *
     * @param \DateTime $tokenDate
     *
     * @return Econtract
     */
    public function setTokenDate($tokenDate)
    {
        $this->tokenDate = $tokenDate;

        return $this;
    }

    /**
     * Get tokenDate
     *
     * @return \DateTime
     */
    public function getTokenDate()
    {
        return $this->tokenDate;
    }

    /**
     * Set tokenactive
     *
     * @param $tokenactive
     *
     * @return Econtract
     */
    public function setTokenactive($tokenactive)
    {
        $this->tokenactive = $tokenactive;

        return $this;
    }

    /**
     * Get tokenactive
     *
     * @return \boolean
     */
    public function getTokenactive()
    {
        return $this->tokenactive;
    }

    /**
     * Set clientSignature
     *
     * @param string $clientSignature
     *
     * @return Econtract
     */
    public function setClientSignature($clientSignature)
    {
        $this->clientSignature = $clientSignature;

        return $this;
    }

    /**
     * Get clientSignature
     *
     * @return string
     */
    public function getClientSignature()
    {
        return $this->clientSignature;
    }

    /**
     * Set patientSigned
     *
     * @param boolean $patientSigned
     *
     * @return Econtract
     */
    public function setPatientSigned($patientSigned)
    {
        $this->patientSigned = $patientSigned;

        return $this;
    }

    /**
     * Get patientSigned
     *
     * @return boolean
     */
    public function getPatientSigned()
    {
        return $this->patientSigned;
    }

    /**
     * Set clientsignDate
     *
     * @param \DateTime $clientsignDate
     *
     * @return Econtract
     */
    public function setClientsignDate($clientsignDate)
    {
        $this->clientsignDate = $clientsignDate;

        return $this;
    }

    /**
     * Get clientsignDate
     *
     * @return \DateTime
     */
    public function getClientsignDate()
    {
        return $this->clientsignDate;
    }

    /**
     * Set emailContent
     *
     * @param string $emailContent
     *
     * @return Econtract
     */
    public function setEmailContent($emailContent)
    {
        $this->emailContent = $emailContent;

        return $this;
    }

    /**
     * Get emailContent
     *
     * @return string
     */
    public function getEmailContent()
    {
        return $this->emailContent;
    }

    /**
     * Set tokenExpiry
     *
     * @param \DateTime $tokenExpiry
     *
     * @return Econtract
     */
    public function setTokenExpiry($tokenExpiry)
    {
        $this->tokenExpiry = $tokenExpiry;

        return $this;
    }

    /**
     * Get tokenExpiry
     *
     * @return \DateTime
     */
    public function getTokenExpiry()
    {
        return $this->tokenExpiry;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Econtract
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
     * Set templateid
     *
     * @param integer $templateid
     *
     * @return Econtract
     */
    public function setTemplateid($templateid)
    {
        $this->templateid = $templateid;

        return $this;
    }

    /**
     * Get templateid
     *
     * @return integer
     */
    public function getTemplateid()
    {
        return $this->templateid;
    }

    /**
     * Set filepath
     *
     * @param string $filepath
     *
     * @return Econtract
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Get filepath
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Set settid
     *
     * @param integer $settid
     *
     * @return Econtract
     */
    public function setSettid($settid)
    {
        $this->settid = $settid;

        return $this;
    }

    /**
     * Get settid
     *
     * @return integer
     */
    public function getSettid()
    {
        return $this->settid;
    }

    /**
     * Set extraContent
     *
     * @param string $extraContent
     *
     * @return Econtract
     */
    public function setExtraContent($extraContent)
    {
        $this->extraContent = $extraContent;

        return $this;
    }

    /**
     * Get extraContent
     *
     * @return string
     */
    public function getExtraContent()
    {
        return $this->extraContent;
    }

    /**
     * Set needSelfSign
     *
     * @param boolean $needSelfSign
     *
     * @return Econtract
     */
    public function setNeedSelfSign($needSelfSign)
    {
        $this->needSelfSign = $needSelfSign;

        return $this;
    }

    /**
     * Get needSelfSign
     *
     * @return boolean
     */
    public function getNeedSelfSign()
    {
        return $this->needSelfSign;
    }
}
