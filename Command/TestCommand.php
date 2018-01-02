<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Command;



use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\ApiService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class TestCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('domainator:test');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $apiService = $this->getContainer()->get(ApiService::class);
        $result = $apiService->createJob('jenkins_template_generic','test');
        dump($result);
        die();

    }
}