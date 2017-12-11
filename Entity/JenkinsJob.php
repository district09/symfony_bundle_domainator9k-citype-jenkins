<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="jenkins_job")
 */
class JenkinsJob
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="JenkinsGroovyScript",mappedBy="jenkinsJob",cascade={"all"})
     */
    protected $jenkinsGroovyScripts;

    /**
     * JenkinsJob constructor.
     */
    public function __construct()
    {
        $this->jenkinsGroovyScripts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
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

    public function addJenkinsGroovyScript(JenkinsGroovyScript $jenkinsGroovyScript){
        $this->jenkinsGroovyScripts->add($jenkinsGroovyScript);
        $jenkinsGroovyScript->setJenkingsJob($this);
    }

    public function removeJenkinsGroovyScript(JenkinsGroovyScript $jenkinsGroovyScript){
        $this->jenkinsGroovyScripts->removeElement($jenkinsGroovyScript);
    }

    public function getJenkinsGroovyScripts(){
        return $this->jenkinsGroovyScripts;
    }

}
