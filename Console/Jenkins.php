<?php

namespace DigipolisGent\Domainator9k\CiTypes\JenkinsBundle\Console;

use DigipolisGent\Domainator9k\CoreBundle\Entity\Settings;
use DigipolisGent\Domainator9k\CoreBundle\Ssh\Shell;
use DigipolisGent\Domainator9k\CoreBundle\Task\Console\AbstractConsole;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Jenkins extends AbstractConsole
{
    protected $console = 'java -jar jenkins-cli.jar -remoting';

    public function getName()
    {
        return 'console.jenkins';
    }

    /**
     * @var Settings
     */
    protected $settings;

    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->settings = $options['settings'];
    }

    protected function configure(OptionsResolver $options)
    {
        parent::configure($options);

        // don't need this here
        if ($options->isDefined('appEnvironment')) {
            $options->remove('appEnvironment');
        }

        $options->setRequired(array('command', 'directory', 'settings'));
        //$options->setAllowedTypes('settings', '\\Digip\\DeployBundle\\Entity\\Settings');
        $options->setDefaults(array(
            'params' => '',
            'pipe_in' => false,
            'shell' => new Shell(),
        ));
    }

    public function execute()
    {
        $result = parent::execute();
        $dir = $this->options['directory'];
        $command = $this->options['command'];
        $params = $this->options['params'];

        $jenkins = $this->settings->getJenkinsCli();
        $url = $this->settings->getJenkinsUrl();
        $keyFile = $this->settings->getJenkinsPrivateKeyFile();
        $keyPass = $this->settings->getJenkinsPrivateKeyPassphrase();

        $keyPass = $keyPass ? "echo \"$keyPass\" | " : '';
        $auth = $keyFile ? '-i '.$keyFile : '';

        $pipe = '';
        if ($this->options['pipe_in']) {
            $pipe = 'echo '.$this->options['pipe_in'].' |';
        }

        $cmd = "cd $dir && $pipe $keyPass java -jar $jenkins -remoting -s $url $auth $command $params";
        $this->doExec($result, $cmd);
        $result->addMessage(sprintf('%s jenkins-cli call: %s', $result->isSuccess() ? 'SUCCESS' : 'FAILED', $command));

        return $result;
    }
}
