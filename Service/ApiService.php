<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

/**
 * Class ApiService
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service
 */
class ApiService
{

    private static $client = null;

    private $url = '';
    private $user = '';
    private $token = '';

    /**
     * ApiService constructor.
     * @param JenkinsServer $jenkinsServer
     */
    public function __construct(JenkinsServer $jenkinsServer)
    {
        $this->url = $jenkinsServer->getUrl() . ':' . $jenkinsServer->getPort();
        $this->user = $jenkinsServer->getUser();
        $this->token = $jenkinsServer->getToken();
    }

    /**
     * @return Client|null
     */
    private function getClient()
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }

    /**
     * @param $jobname
     * @return bool|mixed
     */
    public function getJob($jobname)
    {
        $client = $this->getClient();

        $response = $client->get(
            $this->url . '/job/' . $jobname . '/api/json',
            [
                'auth' => [
                    $this->user,
                    $this->token
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param $templateJobName
     * @param $newJobName
     * @return bool
     */
    public function createJob($templateJobName, $newJobName)
    {
        $client = $this->getClient();

        $client->post(
            $this->url . '/createItem',
            [
                'auth' => [
                    $this->user,
                    $this->token
                ],
                'query' => [
                    'name' => $newJobName,
                    'mode' => 'copy',
                    'from' => $templateJobName,
                ],
            ]
        );
    }

    /**
     * @param $jobName
     */
    public function removeJob($jobName)
    {
        $client = $this->getClient();

        $client->post(
            $this->url . '/job/' . $jobName . '/doDelete',
            [
                'auth' => [
                    $this->user,
                    $this->token
                ]
            ]
        );
    }

    /**
     * @param $script
     */
    public function executeGroovyscript($script)
    {
        $client = $this->getClient();

        $client->post(
            $this->url . '/scriptText',
            [
                'auth' => [
                    $this->user,
                    $this->token
                ],
                'form_params' => [
                    'script' => $script
                ]
            ]
        );
    }

}