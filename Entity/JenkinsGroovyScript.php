<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class JenkinsGroovyScript
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity
 *
 * @ORM\Entity()
 */
class JenkinsGroovyScript
{

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var JenkinsJob
     *
     * @ORM\ManyToOne(targetEntity="JenkinsJob",inversedBy="jenkinsGroovyScripts")
     * @ORM\JoinColumn(referencedColumnName="id",name="jenkins_job_id")
     */
    protected $jenkinsJob;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @param JenkinsJob $jenkinsJob
     */
    public function setJenkingsJob(JenkinsJob $jenkinsJob){
        $this->jenkinsJob = $jenkinsJob;
    }

    /**
     * @return JenkinsJob
     */
    public function getJenkinsJob(){
        return $this->jenkinsJob;
    }
}