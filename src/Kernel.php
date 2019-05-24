<?php

namespace SymEnvSync\SymfonyEnvSync;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {

    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {

    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {

    }
}
