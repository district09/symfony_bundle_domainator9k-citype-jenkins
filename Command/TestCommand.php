<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Command;


use DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Service\JenkinsCliService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{

    public function configure()
    {
        $this->setName('domainator:test');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $jenkinsCliService = $this->getContainer()->get(JenkinsCliService::class);

    }

}