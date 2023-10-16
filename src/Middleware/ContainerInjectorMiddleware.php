<?php

declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Container Injector Middleware
 * https://www.cakedc.com/yevgeny_tomenko/2021/08/09/dependency-injection
 */
class ContainerInjectorMiddleware implements MiddlewareInterface
{
	/**
	 * @var \Cake\Core\ContainerInterface
	 */
	protected ContainerInterface $container;

	/**
	 * Constructor
	 *
	 * @param \Cake\Core\ContainerInterface $container The container to build controllers with.
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Return request with DI container
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request The request.
	 * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
	 * @return \Psr\Http\Message\ResponseInterface A response.
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		return $handler->handle($request->withAttribute('container', $this->container));
	}
}
