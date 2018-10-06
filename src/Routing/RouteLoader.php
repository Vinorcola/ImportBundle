<?php

namespace Vinorcola\ImportBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Vinorcola\ImportBundle\Config\Config;

class RouteLoader extends Loader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * RouteLoader constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $finalRoutes = new RouteCollection();
        /** @var RouteCollection $routeDefinitions */
        $routeDefinitions = $this->import($resource, 'annotation');
        foreach ($this->config->getImportNames() as $importName) {
            $finalRoutes->addCollection($this->loadForImport($routeDefinitions, $importName));
        }

        return $finalRoutes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'vinorcola_import';
    }

    /**
     * Load the routes for an import type.
     *
     * @param RouteCollection $routeDefinitions
     * @param string          $importName
     * @return RouteCollection
     */
    private function loadForImport(RouteCollection $routeDefinitions, string $importName): RouteCollection
    {
        $routeCollection = new RouteCollection();
        foreach ($routeDefinitions as $routeName => $route) {
            $routeCollection->add(
                $this->config->getRouteNamePrefix($importName) . $routeName,
                new Route(
                    $this->config->getUrlPrefix($importName) . $route->getPath(),
                    array_merge($route->getDefaults(), [ 'importName' => $importName ]),
                    $route->getRequirements(),
                    $route->getOptions(),
                    $route->getHost(),
                    $route->getSchemes(),
                    $route->getMethods(),
                    $route->getCondition()
                )
            );
        }

        return $routeCollection;
    }
}
