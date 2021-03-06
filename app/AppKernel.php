<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Core
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

            // Propel
            new Propel\Bundle\PropelBundle\PropelBundle(),
            new Propel\Bundle\PropelAclBundle\PropelAclBundle(),

            // FOSUserBundle
            new FOS\UserBundle\FOSUserBundle(),

            // Admin Generator & dependencies
            new Admingenerator\FormBundle\AdmingeneratorFormBundle(),
            new Admingenerator\FormExtensionsBundle\AdmingeneratorFormExtensionsBundle(),
            new Admingenerator\GeneratorBundle\AdmingeneratorGeneratorBundle($this),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),

            // Project Bundle
            new App\AdminBundle\AppAdminBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
