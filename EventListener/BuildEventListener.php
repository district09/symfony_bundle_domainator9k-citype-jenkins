<?php


namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener;


use DigipolisGent\Domainator9k\CoreBundle\Event\BuildEvent;

/**
 * Class BuildEventListener
 * @package DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\EventListener
 */
class BuildEventListener
{

    /**
     * @param BuildEvent $event
     */
    public function onBuild(BuildEvent $event)
    {
        $applicationEnvironment = $event->getTask()->getApplicationEnvironment();
        $environment = $applicationEnvironment->getEnvironment();

    }
}