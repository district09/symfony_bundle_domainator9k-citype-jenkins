<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use DigipolisGent\Domainator9k\CoreBundle\Entity\Traits\IdentifiableTrait;
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
     * @ORM\Column(name="url", type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min="1", max="255")
     */
    protected $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="port", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    protected $port;

    /**
     * @var string
     *
     * @ORM\Column(name="user",type="string")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="token",type="string")
     */
    protected $token;

    /**
     * @ORM\Column(name="csrf_protected", type="boolean")
     */
    protected $csrfProtected;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isCsrfProtected()
    {
        return $this->csrfProtected;
    }

    /**
     * @param bool $csrfProtected
     */
    public function setCsrfProtected($csrfProtected)
    {
        $this->csrfProtected = $csrfProtected;
    }
}
