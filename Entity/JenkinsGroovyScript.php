<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use DigipolisGent\Domainator9k\CoreBundle\Entity\AbstractApplication;
use DigipolisGent\Domainator9k\CoreBundle\Entity\ApplicationEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\TemplateInterface;
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
 */
class JenkinsGroovyScript implements TemplateInterface
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
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public static function getTemplateEntities(): array
    {
        return [
            'jenkins_job' => JenkinsJob::class,
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
            'jenkins_job:systemName()' => 'getSystemName()',
            'application:serverIps(dev_environment_name)' => 'getApplicationEnvironmentByEnvironmentName(dev_environment_name).getServerIps()',
            'application_environment:serverIps()' => 'getServerIps()',
            'application_environment:environmentName()' => 'getEnvironment().getName()',
            'application:nameCanonical()' => 'getNameCanonical()',
        ];
    }

    /**
     * @return array
     */
    public static function getTemplateMethods(): array
    {
        return [
            'getContent',
        ];
    }
}