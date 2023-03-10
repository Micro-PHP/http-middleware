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

namespace Micro\Plugin\Http\Test\Unit;

use Micro\Component\DependencyInjection\Autowire\ContainerAutowire;
use Micro\Component\DependencyInjection\Container;
use Micro\Framework\Kernel\KernelInterface;
use Micro\Plugin\Http\Decorator\HttpMiddlewareDecorator;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Micro\Plugin\Http\HttpCorePlugin;
use Micro\Plugin\Http\HttpMiddlewarePlugin;
use Micro\Plugin\Http\HttpMiddlewarePluginConfiguration;
use PHPUnit\Framework\TestCase;

class HttpMiddlewarePluginTest extends TestCase
{
    private HttpMiddlewarePlugin $plugin;

    public function testPlugin(): void
    {
        $this->plugin = new HttpMiddlewarePlugin();
        $cfg = $this->createMock(HttpMiddlewarePluginConfiguration::class);
        $cfg->expects($this->once())
            ->method('getDecorationPriority')
            ->willReturn(900);

        $container = new ContainerAutowire(new Container());
        $container->register(KernelInterface::class, fn () => $this->createMock(KernelInterface::class));
        $container->register(HttpFacadeInterface::class, fn () => $this->createMock(HttpFacadeInterface::class));

        $this->plugin->setConfiguration($cfg);
        $this->plugin->provideDependencies($container);

        $this->assertInstanceOf(HttpMiddlewareDecorator::class, $container->get(HttpFacadeInterface::class));

        $this->assertEquals([
            HttpCorePlugin::class,
        ], $this->plugin->getDependedPlugins());
    }
}
