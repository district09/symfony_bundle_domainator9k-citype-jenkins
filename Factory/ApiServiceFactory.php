<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;

/**
 * Class ApiServiceFactory
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Factory
 */
class ApiServiceFactory
{

    public function create(JenkinsServer $jenkinsServer)
    {
        return new ApiService($jenkinsServer);
    }
}
