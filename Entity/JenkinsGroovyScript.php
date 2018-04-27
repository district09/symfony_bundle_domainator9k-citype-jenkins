<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use DigipolisGent\Domainator9k\CoreBundle\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class JenkinsGroovyScript
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity
 *
 * @ORM\Entity()
 * @UniqueEntity(fields={"name"})
 * @ORM\Table(name="jenkins_groovy_script")
 */
class JenkinsGroovyScript
{

    use IdentifiableTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="name",type="string")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="content",type="text")
     * @Assert\NotBlank()
     */
    protected $content;

    /**
     * @var int
     *
     * @ORM\Column(name="script_order",type="integer")
     * @Assert\NotBlank()
     */
    protected $order;


    public function __construct()
    {
        $this->jenkinsJobs = new ArrayCollection();
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

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
    }
}
