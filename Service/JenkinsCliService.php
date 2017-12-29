<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service;

/**
 * Class JenkinsCliService
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service
 */
class JenkinsCliService
{

    private $javaPath;
    private $cliPath;
    private $privateKeyPath;
    private $url;

    /**
     * @param string $javaPath
     */
    public function setJavaPath(string $javaPath){
        $this->javaPath = $javaPath;
    }

    /**
     * @param string $cliPath
     */
    public function setCliPath(string $cliPath){
        $this->cliPath = $cliPath;
    }

    /**
     * @param string $privateKeyPath
     */
    public function setPrivateKeyPath(string $privateKeyPath){
        $this->privateKeyPath = $privateKeyPath;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url){
        $this->url = $url;
    }

}