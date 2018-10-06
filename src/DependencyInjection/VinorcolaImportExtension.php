<?php

namespace Vinorcola\ImportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Vinorcola\ImportBundle\Config\Config;

class VinorcolaImportExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $this->setupConfigService($container, $processedConfig);
    }

    private function setupConfigService(ContainerBuilder $container, array $config)
    {
        $configService = $container->getDefinition(Config::class);
        foreach ($config['imports'] as &$importConfig) {
            $importConfig['service'] = new Reference($importConfig['service']);
        }
        $configService->setArgument('$config', $config);
    }
}
