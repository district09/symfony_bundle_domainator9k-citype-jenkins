<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;


use DigipolisGent\Domainator9k\CoreBundle\Entity\TemplateInterface;
use DigipolisGent\Domainator9k\CoreBundle\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class JenkinsJob
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity
 *
 * @ORM\Entity()
 */
class JenkinsJob implements TemplateInterface
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
     * @ORM\Column(name="system_name",type="string")
     * @Assert\NotBlank()
     */
    protected $systemName;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="JenkinsGroovyScript", inversedBy="jenkinsJobs",cascade={"persist"})
     * @ORM\JoinTable(name="jenkins_job_jenkins_groovy_script")
     */
    protected $jenkinsGroovyScripts;

    public function __construct()
    {
        $this->jenkinsGroovyScripts = new ArrayCollection();
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


    public function setJenkinsGroovyScripts($groovyScripts)
    {
        $this->jenkinsGroovyScripts = $groovyScripts;
    }

    /**
     * @return ArrayCollection
     */
    public function getJenkinsGroovyScripts()
    {
        return $this->jenkinsGroovyScripts->toArray();
    }

    /**
     * @return string
     */
    public function getSystemName()
    {
        return $this->systemName;
    }

    /**
     * @param string $systemName
     */
    public function setSystemName(string $systemName)
    {
        $this->systemName = $systemName;
    }

    public function __clone()
    {
        $this->id = null;
    }

    /**
     * @return array
     */
    public static function getTemplateEntities(): array
    {
        return [
            'application' => AbstractApplication::class,
            'application_environment' => ApplicationEnvironment::class,
        ];
    }

    /**
     * @return array
     */
    public static function getTemplateReplacements(): array
    {
        return [
            'application:nameCanonical()' => 'getNameCanonical()',
            'application_environment:name()' => 'getEnvironment().getName()',
        ];
    }

    /**
     * @return array
     */
    public static function getTemplateMethods(): array
    {
        return [
            'getSystemName',
        ];
    }
}