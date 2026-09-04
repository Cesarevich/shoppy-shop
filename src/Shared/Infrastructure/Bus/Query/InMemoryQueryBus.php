<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Bus\Query\QueryNotRegisteredError;
use App\Shared\Domain\Bus\Query\Response;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;

final class InMemoryQueryBus implements QueryBus
{
    /** @var array<class-string<Query>, callable(Query): Response> */
    private array $handlers = [];

    /** @param iterable<object> $queryHandlers */
    public function __construct(iterable $queryHandlers)
    {
        foreach ($queryHandlers as $handler) {
            $queryClass = $this->queryClassFrom($handler);
            $this->handlers[$queryClass] = $this->asInvoker($handler);
        }
    }

    public function ask(Query $query): Response
    {
        $queryClass = $query::class;
        if (!isset($this->handlers[$queryClass])) {
            throw new QueryNotRegisteredError($query);
        }

        return ($this->handlers[$queryClass])($query);
    }

    /** @return class-string<Query> */
    private function queryClassFrom(object $handler): string
    {
        $method = (new ReflectionClass($handler))->getMethod('__invoke');
        if (1 !== $method->getNumberOfParameters()) {
            throw new LogicException(sprintf('Handler <%s> __invoke must have exactly one parameter.', $handler::class));
        }

        $type = $method->getParameters()[0]->getType();
        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            throw new LogicException(sprintf('Handler <%s> __invoke must type-hint a query class.', $handler::class));
        }

        $queryClass = $type->getName();
        if (!is_a($queryClass, Query::class, true)) {
            throw new LogicException(sprintf('Handler <%s> must handle a Query, got <%s>.', $handler::class, $queryClass));
        }

        return $queryClass;
    }

    /** @return callable(Query): Response */
    private function asInvoker(object $handler): callable
    {
        return static function (Query $query) use ($handler): Response {
            $result = $handler->__invoke($query); // @phpstan-ignore method.notFound
            if (!$result instanceof Response) {
                throw new LogicException(sprintf('Query handler <%s> must return a Response.', $handler::class));
            }

            return $result;
        };
    }
}
