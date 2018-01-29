<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Provider;

use DigipolisGent\SettingBundle\Provider\DataTypeProviderInterface;

/**
 * Class DataTypeProvider
 * @package DigipolisGent\Domainator9k\CoreBundle\Provider
 */
class DataTypeProvider implements DataTypeProviderInterface
{

    /**
     * @return array
     */
    public function getDataTypes()
    {
        return [
            [
                'key' => 'jenkins_server',
                'label' => 'Jenkins server',
                'required' => true,
                'field_type' => 'jenkins_server_choice',
                'entity_types' => ['application_environment'],
            ],
            [
                'key' => 'jenkins_job',
                'label' => 'Jenkins job',
                'required' => true,
                'field_type' => 'jenkins_job',
                'entity_types' => ['application_type_environment', 'application_environment'],
            ]
        ];
    }
}
