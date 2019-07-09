<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service;

use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Entity\JenkinsServer;
use GuzzleHttp\Client;

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
    private $csrfProtected = false;

    /**
     * ApiService constructor.
     * @param JenkinsServer $jenkinsServer
     */
    public function __construct(JenkinsServer $jenkinsServer)
    {
        $this->url = $jenkinsServer->getUrl() . ':' . $jenkinsServer->getPort();
        $this->user = $jenkinsServer->getUser();
        $this->token = $jenkinsServer->getToken();
        $this->csrfProtected = $jenkinsServer->isCsrfProtected();
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
        return $this->get($this->url . '/job/' . $jobname . '/api/json');
    }

    /**
     * @param $templateJobName
     * @param $newJobName
     * @return bool
     */
    public function createJob($templateJobName, $newJobName)
    {
        $this->post(
            $this->url . '/createItem',
            [
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
        $this->post($this->url . '/job/' . $jobName . '/doDelete');
    }

    /**
     * @param $script
     */
    public function executeGroovyscript($script)
    {
        $this->post(
            $this->url . '/scriptText',
            [
                'form_params' => [
                    'script' => $script
                ],
            ]
        );
    }

    protected function get($uri, $options = [])
    {
        $client = $this->getClient();

        $options += $this->getDefaultOptions();

        $response = $client->get($uri, $options);

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function post($uri, $options = [])
    {
        $client = $this->getClient();

        $options += $this->getDefaultOptions();

        if ($this->csrfProtected) {
            $token = $this->get($this->url . '/crumbIssuer/api/json');
            if ($token && isset($token->crumbRequestField)) {
                $options['headers'][$token->crumbRequestField] = $token->crumb;
            }
        }

        $client->post($uri, $options);
    }


    protected function getDefaultOptions()
    {
        return [
            'auth' => [
                $this->user,
                $this->token
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
    }
}
