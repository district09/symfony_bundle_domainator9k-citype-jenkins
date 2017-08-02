<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="groovyscript")
 */
class GroovyScript
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
     * @ORM\Column(type="string")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings", inversedBy="deployJobGroovyScripts")
     * @ORM\JoinColumn(name="deploy_ciapptypesettings_id", referencedColumnName="id")
     */
    protected $deployJobCiAppTypeSetting;

    /**
     * @ORM\ManyToOne(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings", inversedBy="revertJobGroovyScripts")
     * @ORM\JoinColumn(name="revert_ciapptypesettings_id", referencedColumnName="id")
     */
    protected $revertJobCiAppTypeSetting;

    /**
     * @ORM\ManyToOne(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings", inversedBy="syncJobGroovyScripts")
     * @ORM\JoinColumn(name="sync_ciapptypesettings_id", referencedColumnName="id")
     */
    protected $syncJobCiAppTypeSetting;

    /**
     * @ORM\ManyToOne(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings", inversedBy="dumpJobGroovyScripts")
     * @ORM\JoinColumn(name="dump_ciapptypesettings_id", referencedColumnName="id")
     */
    protected $dumpJobCiAppTypeSetting;

    /**
     * @ORM\ManyToOne(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings", inversedBy="validateJobGroovyScripts")
     * @ORM\JoinColumn(name="validate_ciapptypesettings_id", referencedColumnName="id")
     */
    protected $validateJobCiAppTypeSetting;

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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getDeployJobCiAppTypeSetting()
    {
        return $this->deployJobCiAppTypeSetting;
    }

    /**
     * @param mixed $deployJobCiAppTypeSetting
     */
    public function setDeployJobCiAppTypeSetting($deployJobCiAppTypeSetting)
    {
        $this->deployJobCiAppTypeSetting = $deployJobCiAppTypeSetting;
    }

    /**
     * @return mixed
     */
    public function getRevertJobCiAppTypeSetting()
    {
        return $this->revertJobCiAppTypeSetting;
    }

    /**
     * @param mixed $revertJobCiAppTypeSetting
     */
    public function setRevertJobCiAppTypeSetting($revertJobCiAppTypeSetting)
    {
        $this->revertJobCiAppTypeSetting = $revertJobCiAppTypeSetting;
    }

    /**
     * @return mixed
     */
    public function getSyncJobCiAppTypeSetting()
    {
        return $this->syncJobCiAppTypeSetting;
    }

    /**
     * @param mixed $syncJobCiAppTypeSetting
     */
    public function setSyncJobCiAppTypeSetting($syncJobCiAppTypeSetting)
    {
        $this->syncJobCiAppTypeSetting = $syncJobCiAppTypeSetting;
    }

    /**
     * @return mixed
     */
    public function getDumpJobCiAppTypeSetting()
    {
        return $this->dumpJobCiAppTypeSetting;
    }

    /**
     * @param mixed $dumpJobCiAppTypeSetting
     */
    public function setDumpJobCiAppTypeSetting($dumpJobCiAppTypeSetting)
    {
        $this->dumpJobCiAppTypeSetting = $dumpJobCiAppTypeSetting;
    }

    /**
     * @return mixed
     */
    public function getValidateJobCiAppTypeSetting()
    {
        return $this->validateJobCiAppTypeSetting;
    }

    /**
     * @param mixed $validateJobCiAppTypeSetting
     */
    public function setValidateJobCiAppTypeSetting($validateJobCiAppTypeSetting)
    {
        $this->validateJobCiAppTypeSetting = $validateJobCiAppTypeSetting;
    }
}
