<?php

declare(strict_types=1);

/*
 *  This file is part of the Micro framework package.
 *
 *  (c) Stanislau Komar <kost@micro-php.net>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Micro\Plugin\Http;

use Micro\Component\DependencyInjection\Container;
use Micro\Framework\Kernel\KernelInterface;
use Micro\Framework\Kernel\Plugin\ConfigurableInterface;
use Micro\Framework\Kernel\Plugin\DependencyProviderInterface;
use Micro\Framework\Kernel\Plugin\PluginConfigurationTrait;
use Micro\Framework\Kernel\Plugin\PluginDependedInterface;
use Micro\Plugin\Http\Business\Executor\RouteExecutorFactoryInterface;
use Micro\Plugin\Http\Business\Executor\RouteMiddlewareExecutorFactory;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocatorFactory;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocatorFactoryInterface;
use Micro\Plugin\Http\Configuration\HttpMiddlewarePluginConfigurationInterface;
use Micro\Plugin\Http\Decorator\HttpMiddlewareDecorator;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;

/**
 * @author Stanislau Komar <head.trackingsoft@gmail.com>
 *
 * @method HttpMiddlewarePluginConfigurationInterface configuration()
 */
class HttpMiddlewarePlugin implements PluginDependedInterface, DependencyProviderInterface, ConfigurableInterface
{
    use PluginConfigurationTrait;

    private HttpFacadeInterface $decorated;

    private KernelInterface $kernel;

    public function provideDependencies(Container $container): void
    {
        $container->decorate(HttpFacadeInterface::class,
            function (HttpFacadeInterface $decorated, KernelInterface $kernel) { // @phpstan-ignore-line
                $this->decorated = $decorated;
                $this->kernel = $kernel;

                return $this->createDecorator();
            },
            $this->configuration()->getDecorationPriority()
        );
    }

    protected function createDecorator(): HttpFacadeInterface
    {
        return new HttpMiddlewareDecorator(
            $this->decorated,
            $this->createRouteExecutorFactory()
        );
    }

    protected function createRouteExecutorFactory(): RouteExecutorFactoryInterface
    {
        return new RouteMiddlewareExecutorFactory(
            $this->decorated,
            $this->createMiddlewareLocatorFactory()
        );
    }

    protected function createMiddlewareLocatorFactory(): MiddlewareLocatorFactoryInterface
    {
        return new MiddlewareLocatorFactory($this->kernel);
    }

    public function getDependedPlugins(): iterable
    {
        return [
            HttpCorePlugin::class,
        ];
    }
}
