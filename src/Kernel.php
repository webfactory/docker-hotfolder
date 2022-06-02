<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        $contents = require __DIR__.'/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir().'/src/config_'.$this->getEnvironment().'.yml');
    }

    /*
     * Nur notwendig für Symfony 3.4:
     */
    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * Passend zu unserem Splunk-Setup.
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/logs';
    }
}
