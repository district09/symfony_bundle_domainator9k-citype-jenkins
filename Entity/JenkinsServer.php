<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use DigipolisGent\Domainator9k\CoreBundle\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="jenkins_server")
 */
class JenkinsServer
{

    use IdentifiableTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="name",type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="jenkins_url", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min="1", max="255")
     */
    protected $jenkinsUrl;

    /**
     * @var string
     * @ORM\Column(name="jenkins_cli", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min="1", max="255")
     */
    protected $jenkinsCli;

    /**
     * @var string
     * @ORM\Column(name="jenkins_private_key_file", type="string", nullable=true)
     * @Assert\Length(min="1", max="255")
     * @Assert\NotBlank()
     */
    protected $jenkinsPrivateKeyFile;

    /**
     * @var string
     * @ORM\Column(name="jenkins_private_key_passphrase", type="string", nullable=true)
     * @Assert\Length(max="255")
     * @Assert\NotBlank()
     */
    protected $jenkinsPrivateKeyPassphrase;

    /**
     * @return string
     */
    public function getJenkinsUrl()
    {
        return $this->jenkinsUrl;
    }

    /**
     * @param string $jenkinsUrl
     *
     * @return $this
     */
    public function setJenkinsUrl($jenkinsUrl)
    {
        $this->jenkinsUrl = $jenkinsUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getJenkinsCli()
    {
        return $this->jenkinsCli;
    }

    /**
     * @param string $jenkinsCli
     *
     * @return $this
     */
    public function setJenkinsCli($jenkinsCli)
    {
        $this->jenkinsCli = $jenkinsCli;

        return $this;
    }

    /**
     * @return string
     */
    public function getJenkinsPrivateKeyFile()
    {
        return $this->jenkinsPrivateKeyFile;
    }

    /**
     * @param string $jenkinsPrivateKeyFile
     *
     * @return $this
     */
    public function setJenkinsPrivateKeyFile($jenkinsPrivateKeyFile)
    {
        $this->jenkinsPrivateKeyFile = $jenkinsPrivateKeyFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getJenkinsPrivateKeyPassphrase()
    {
        return $this->jenkinsPrivateKeyPassphrase;
    }

    /**
     * @param string $jenkinsPrivateKeyPassphrase
     *
     * @return $this
     */
    public function setJenkinsPrivateKeyPassphrase($jenkinsPrivateKeyPassphrase)
    {
        $this->jenkinsPrivateKeyPassphrase = $jenkinsPrivateKeyPassphrase;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}
