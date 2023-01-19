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
use Micro\Plugin\Http\Business\Executor\RouteMiddlewareExecutorFactory;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocatorFactoryInterface;
use Micro\Plugin\Http\Business\Middleware\MiddlewareLocatorInterface;
use PHPUnit\Framework\TestCase;

class RouteMiddlewareExecutorFactoryTest extends TestCase
{
    public function testCreate()
    {
        $decorated = $this->createMock(RouteExecutorInterface::class);
        $middlewareLocator = $this->createMock(MiddlewareLocatorInterface::class);
        $middlewareLocatorFactory = $this->createMock(MiddlewareLocatorFactoryInterface::class);
        $middlewareLocatorFactory->method('create')->willReturn($middlewareLocator);

        $factory = new RouteMiddlewareExecutorFactory($decorated, $middlewareLocatorFactory);
        $this->assertInstanceOf(RouteMiddlewareExecutor::class, $factory->create());
    }
}
