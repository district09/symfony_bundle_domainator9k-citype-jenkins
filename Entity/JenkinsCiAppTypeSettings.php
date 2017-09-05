<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DigipolisGent\Domainator9k\CoreBundle\Entity\BaseCiAppTypeSettings;

/**
 * @ORM\Entity
 * @ORM\Table(name="jenkins_apptype_settings")
 */
class JenkinsCiAppTypeSettings extends BaseCiAppTypeSettings
{
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $deployJobEnabled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $deployJobTemplate;

    /**
     * @ORM\OneToMany(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript", mappedBy="deployJobCiAppTypeSetting",cascade={"persist"}, orphanRemoval=true)
     */
    protected $deployJobGroovyScripts;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $revertJobEnabled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $revertJobTemplate;

    /**
     * @ORM\OneToMany(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript", mappedBy="revertJobCiAppTypeSetting",cascade={"persist"}, orphanRemoval=true)
     */
    protected $revertJobGroovyScripts;
    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $syncJobEnabled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $syncJobTemplate;

    /**
     * @ORM\OneToMany(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript", mappedBy="syncJobCiAppTypeSetting",cascade={"persist"}, orphanRemoval=true)
     */
    protected $syncJobGroovyScripts;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $dumpJobEnabled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $dumpJobTemplate;

    /**
     * @ORM\OneToMany(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript", mappedBy="dumpJobCiAppTypeSetting",cascade={"persist"}, orphanRemoval=true)
     */
    protected $dumpJobGroovyScripts;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $validateJobEnabled;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $validateJobTemplate;

    /**
     * @ORM\OneToMany(targetEntity="DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\GroovyScript", mappedBy="validateJobCiAppTypeSetting",cascade={"persist"}, orphanRemoval=true)
     */
    protected $validateJobGroovyScripts;

    public function __construct($ciTypeSlug, $appTypeSlug)
    {
        parent::__construct($ciTypeSlug, $appTypeSlug);

        $this->deployJobGroovyScripts = new ArrayCollection();
        $this->revertJobGroovyScripts = new ArrayCollection();
        $this->syncJobGroovyScripts = new ArrayCollection();
        $this->dumpJobGroovyScripts = new ArrayCollection();
        $this->validateJobGroovyScripts = new ArrayCollection();
    }

    /**
     * @return bool
     */
    public function isDeployJobEnabled()
    {
        return $this->deployJobEnabled;
    }

    /**
     * @param bool $deployJobEnabled
     */
    public function setDeployJobEnabled($deployJobEnabled)
    {
        $this->deployJobEnabled = $deployJobEnabled;
    }

    /**
     * @return bool
     */
    public function isRevertJobEnabled()
    {
        return $this->revertJobEnabled;
    }

    /**
     * @param bool $revertJobEnabled
     */
    public function setRevertJobEnabled($revertJobEnabled)
    {
        $this->revertJobEnabled = $revertJobEnabled;
    }

    /**
     * @return bool
     */
    public function isSyncJobEnabled()
    {
        return $this->syncJobEnabled;
    }

    /**
     * @param bool $syncJobEnabled
     */
    public function setSyncJobEnabled($syncJobEnabled)
    {
        $this->syncJobEnabled = $syncJobEnabled;
    }

    /**
     * @return bool
     */
    public function isDumpJobEnabled()
    {
        return $this->dumpJobEnabled;
    }

    /**
     * @param bool $dumpJobEnabled
     */
    public function setDumpJobEnabled($dumpJobEnabled)
    {
        $this->dumpJobEnabled = $dumpJobEnabled;
    }

    /**
     * @return bool
     */
    public function isValidateJobEnabled()
    {
        return $this->validateJobEnabled;
    }

    /**
     * @param bool $validateJobEnabled
     */
    public function setValidateJobEnabled($validateJobEnabled)
    {
        $this->validateJobEnabled = $validateJobEnabled;
    }

    /**
     * @return string
     */
    public function getDeployJobTemplate()
    {
        return $this->deployJobTemplate;
    }

    /**
     * @param string $deployJobTemplate
     */
    public function setDeployJobTemplate($deployJobTemplate)
    {
        $this->deployJobTemplate = $deployJobTemplate;
    }

    /**
     * @return string
     */
    public function getRevertJobTemplate()
    {
        return $this->revertJobTemplate;
    }

    /**
     * @param string $revertJobTemplate
     */
    public function setRevertJobTemplate($revertJobTemplate)
    {
        $this->revertJobTemplate = $revertJobTemplate;
    }

    /**
     * @return string
     */
    public function getSyncJobTemplate()
    {
        return $this->syncJobTemplate;
    }

    /**
     * @param string $syncJobTemplate
     */
    public function setSyncJobTemplate($syncJobTemplate)
    {
        $this->syncJobTemplate = $syncJobTemplate;
    }

    /**
     * @return string
     */
    public function getDumpJobTemplate()
    {
        return $this->dumpJobTemplate;
    }

    /**
     * @param string $dumpJobTemplate
     */
    public function setDumpJobTemplate($dumpJobTemplate)
    {
        $this->dumpJobTemplate = $dumpJobTemplate;
    }

    /**
     * @return string
     */
    public function getValidateJobTemplate()
    {
        return $this->validateJobTemplate;
    }

    /**
     * @param string $validateJobTemplate
     */
    public function setValidateJobTemplate($validateJobTemplate)
    {
        $this->validateJobTemplate = $validateJobTemplate;
    }

    //groovy script stuff

    /**
     * @return ArrayCollection|GroovyScript[]
     */
    public function getDeployJobGroovyScripts()
    {
        return $this->deployJobGroovyScripts;
    }

    public function setDeployJobGroovyScripts($deployJobGroovyScripts)
    {
        $this->deployJobGroovyScripts = $deployJobGroovyScripts;
    }

    public function addDeployJobGroovyScript(GroovyScript $deployJobGroovyScript)
    {
        $deployJobGroovyScript->setDeployJobCiAppTypeSetting($this);
        $this->deployJobGroovyScripts[] = $deployJobGroovyScript;
    }

    public function removeDeployJobGroovyScript(GroovyScript $deployJobGroovyScript)
    {
        $this->deployJobGroovyScripts->removeElement($deployJobGroovyScript);
        $deployJobGroovyScript->setDeployJobCiAppTypeSetting(null);
    }

    /**
     * @return ArrayCollection|GroovyScript[]
     */
    public function getRevertJobGroovyScripts()
    {
        return $this->revertJobGroovyScripts;
    }

    public function setRevertJobGroovyScripts($jobGroovyScripts)
    {
        $this->revertJobGroovyScripts = $jobGroovyScripts;
    }

    public function addRevertJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $jobGroovyScript->setRevertJobCiAppTypeSetting($this);
        $this->revertJobGroovyScripts[] = $jobGroovyScript;
    }

    public function removeRevertJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $this->revertJobGroovyScripts->removeElement($jobGroovyScript);
        $jobGroovyScript->setRevertJobCiAppTypeSetting(null);
    }

    /**
     * @return ArrayCollection|GroovyScript[]
     */
    public function getSyncJobGroovyScripts()
    {
        return $this->syncJobGroovyScripts;
    }

    public function setSyncJobGroovyScripts($jobGroovyScripts)
    {
        $this->syncJobGroovyScripts = $jobGroovyScripts;
    }

    public function addSyncJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $jobGroovyScript->setSyncJobCiAppTypeSetting($this);
        $this->syncJobGroovyScripts[] = $jobGroovyScript;
    }

    public function removeSyncJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $this->syncJobGroovyScripts->removeElement($jobGroovyScript);
        $jobGroovyScript->setSyncJobCiAppTypeSetting(null);
    }

    /**
     * @return ArrayCollection|GroovyScript[]
     */
    public function getDumpJobGroovyScripts()
    {
        return $this->dumpJobGroovyScripts;
    }

    public function setDumpJobGroovyScripts($jobGroovyScripts)
    {
        $this->dumpJobGroovyScripts = $jobGroovyScripts;
    }

    public function addDumpJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $jobGroovyScript->setDumpJobCiAppTypeSetting($this);
        $this->dumpJobGroovyScripts[] = $jobGroovyScript;
    }

    public function removeDumpJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $this->dumpJobGroovyScripts->removeElement($jobGroovyScript);
        $jobGroovyScript->setDumpJobCiAppTypeSetting(null);
    }

    /**
     * @return ArrayCollection|GroovyScript[]
     */
    public function getValidateJobGroovyScripts()
    {
        return $this->validateJobGroovyScripts;
    }

    public function setValidateJobGroovyScripts($jobGroovyScripts)
    {
        $this->validateJobGroovyScripts = $jobGroovyScripts;
    }

    public function addValidateJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $jobGroovyScript->setValidateJobCiAppTypeSetting($this);
        $this->validateJobGroovyScripts[] = $jobGroovyScript;
    }

    public function removeValidateJobGroovyScript(GroovyScript $jobGroovyScript)
    {
        $this->validateJobGroovyScripts->removeElement($jobGroovyScript);
        $jobGroovyScript->setValidateJobCiAppTypeSetting(null);
    }
}
