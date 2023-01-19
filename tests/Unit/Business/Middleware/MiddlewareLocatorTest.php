<?php

declare(strict_types=1);

/**
 * This file is part of the Micro framework package.
 *
 * (c) Stanislau Komar <head.trackingsoft@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Micro\Plugin\Http\Test\Unit\Business\Middleware;

use Micro\Framework\Kernel\KernelInterface;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocator;
use Micro\Plugin\Http\Plugin\HttpMiddlewareOrderedPluginInterface;
use Micro\Plugin\Http\Plugin\HttpMiddlewarePluginInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class MiddlewareLocatorTest extends TestCase
{
    /** @var KernelInterface */
    private $kernel;

    /** @var MiddlewareLocator */
    private $locator;

    public function setUp(): void
    {
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->locator = new MiddlewareLocator($this->kernel);
    }

    public function testLocate()
    {
        $request = Request::create('/one/two/three/1/success');

        $mc = $this->createMiddlewareCollection($request);
        $this->kernel->expects($this->once())
            ->method('plugins')
            ->willReturn($mc);

        $located = 0;
        foreach ($this->locator->locate($request) as $middleware) {
            ++$located;

            $this->assertInstanceOf(HttpMiddlewarePluginInterface::class, $middleware);
        }

        $this->assertEquals(5, $located);

    }

    public function middlewareCollectionConfig(): array
    {
        return [
            // Success
            [ '^/one/two', 3 ],
            [ '/three', 1 ],
            [ '^/one', null ],
            [ '^/one/(\b[a-z]+)/three', 2],
            ['^/One/(\b[a-z]+)/ThreE/(\d+)/success', 4],

            // will no executed
            [ '^/one$', null ],
            ['/none', null],
            ['/(\d+)/$', null],
            [ '^/one/(\b[a-z]+)/three/four', 2 ],
            [ '^/one/two/three/four', null ],
            [ '/one/two/three/four/', null ],
        ];
    }

    protected function createMiddlewareCollection(Request $request)
    {
        $config = $this->middlewareCollectionConfig();

        $middlewares = [];

        foreach ($config as $mc) {
            $middlewares []= $this->createMiddlewareObj(
                $request,
                $mc[0],
                $mc[1],
            );
        }

        return new \ArrayObject($middlewares);
    }

    protected function createMiddlewareObj(Request $request, string $path, int|null $priority)
    {
        if(!$priority) {
            $middleware = $this->createMock(HttpMiddlewarePluginInterface::class);
        } else {
            $middleware = $this->createMock(HttpMiddlewareOrderedPluginInterface::class);
            $middleware->method('getMiddlewarePriority')->willReturn($priority);
        }

        $middleware->expects($this->once())
            ->method('getRequestMatchPath')
            ->willReturn($path);

        return $middleware;
    }
}


