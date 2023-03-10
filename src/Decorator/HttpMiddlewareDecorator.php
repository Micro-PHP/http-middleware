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

namespace Micro\Plugin\Http\Decorator;

use Micro\Plugin\Http\Business\Executor\RouteExecutorFactoryInterface;
use Micro\Plugin\Http\Business\Route\RouteBuilderInterface;
use Micro\Plugin\Http\Business\Route\RouteInterface;
use Micro\Plugin\Http\Facade\HttpFacadeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Stanislau Komar <head.trackingsoft@gmail.com>
 */
readonly class HttpMiddlewareDecorator implements HttpFacadeInterface
{
    public function __construct(
        private HttpFacadeInterface $decorated,
        private RouteExecutorFactoryInterface $routeExecutorFactory
    ) {
    }

    public function getDeclaredRoutesNames(): iterable
    {
        return $this->decorated->getDeclaredRoutesNames();
    }

    public function createRouteBuilder(): RouteBuilderInterface
    {
        return $this->decorated->createRouteBuilder();
    }

    public function execute(Request $request, bool $flush = true): Response
    {
        return $this->routeExecutorFactory
            ->create()
            ->execute($request, $flush);
    }

    public function generateUrlByRouteName(string $routeName, ?array $parameters = []): string
    {
        return $this->decorated->generateUrlByRouteName($routeName, $parameters);
    }

    public function match(Request $request): RouteInterface
    {
        return $this->decorated->match($request);
    }
}
