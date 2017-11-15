<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\CiType;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsCiAppTypeSettings;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsSettings;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\JenkinsCiAppTypeSettingsType;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Form\JenkinsTypeSettingsType;
use DigipolisGent\Domainator9k\CoreBundle\Entity\AppEnvironment;
use DigipolisGent\Domainator9k\CoreBundle\Entity\BaseCiType;
use DigipolisGent\Domainator9k\CoreBundle\Interfaces\CiTypeSettingsInterface;

/**
 * Class JenkinsCiType.
 */
class JenkinsCiType extends BaseCiType
{
    public function getSettingsFormClass()
    {
        return JenkinsTypeSettingsType::class;
    }

    public function getSettingsEntityClass()
    {
        return JenkinsSettings::class;
    }

    public function getAppTypeSettingsFormClass()
    {
        return JenkinsCiAppTypeSettingsType::class;
    }

    public function getAppTypeSettingsEntityClass()
    {
        return JenkinsCiAppTypeSettings::class;
    }

    public function getProcessorServiceClass()
    {
        return 'digip_deploy.ci_processor.jenkins';
    }

    public function getMenuUrlFieldName()
    {
        return 'jenkinsUrl';
    }

    /**
     * @param CiTypeSettingsInterface $settings
     * @param AppEnvironment  $env
     *
     * @return string
     */
    public function buildCiUrl(CiTypeSettingsInterface $settings, AppEnvironment $env)
    {
        return $settings->getJenkinsUrl().'job/'.$env->getFullNameCanonical();
    }

    /**
     * @param JenkinsSettings $ciTypeSettings
     *
     * @return mixed
     */
    public function buildUrl(CiTypeSettingsInterface $ciTypeSettings)
    {
        return $ciTypeSettings->getJenkinsUrl();
    }
}
