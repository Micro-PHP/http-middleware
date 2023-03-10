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

namespace Micro\Plugin\Http\Test\Unit\Business\Executor;

use Micro\Plugin\Http\Business\Executor\RouteExecutorInterface;
use Micro\Plugin\Http\Business\Executor\RouteMiddlewareExecutor;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocatorInterface;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Micro\Plugin\Http\Plugin\HttpMiddlewarePluginInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RouteMiddlewareExecutorTest extends TestCase
{
    private HttpFacadeInterface $httpFacade;

    private MiddlewareLocatorInterface $middlewareLocator;

    private RouteExecutorInterface $routeExecutor;

    private Request $request;

    protected function setUp(): void
    {
        $this->request = Request::create('/');
        $this->httpFacade = $this->createMock(HttpFacadeInterface::class);
        $this->middlewareLocator = $this->createMock(MiddlewareLocatorInterface::class);
        $this->middlewareLocator
            ->expects($this->once())
            ->method('locate')
            ->with($this->request)
            ->willReturn(
                new \ArrayObject([
                    $this->createMock(HttpMiddlewarePluginInterface::class),
                    $this->createMock(HttpMiddlewarePluginInterface::class),
                    $this->createMock(HttpMiddlewarePluginInterface::class),
                ])
            );

        $this->routeExecutor = new RouteMiddlewareExecutor(
            $this->httpFacade,
            $this->middlewareLocator
        );
    }

    public function testExecute()
    {
        $this->routeExecutor->execute($this->request);
    }
}
