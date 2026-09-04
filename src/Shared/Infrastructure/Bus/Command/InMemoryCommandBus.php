<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Command\CommandNotRegisteredError;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;

final class InMemoryCommandBus implements CommandBus
{
    /** @var array<class-string<Command>, callable(Command): void> */
    private array $handlers = [];

    /** @param iterable<object> $commandHandlers */
    public function __construct(iterable $commandHandlers)
    {
        foreach ($commandHandlers as $handler) {
            $commandClass = $this->commandClassFrom($handler);
            $this->handlers[$commandClass] = $this->asInvoker($handler);
        }
    }

    public function dispatch(Command $command): void
    {
        $commandClass = $command::class;
        if (!isset($this->handlers[$commandClass])) {
            throw new CommandNotRegisteredError($command);
        }

        ($this->handlers[$commandClass])($command);
    }

    /** @return class-string<Command> */
    private function commandClassFrom(object $handler): string
    {
        $method = (new ReflectionClass($handler))->getMethod('__invoke');
        if (1 !== $method->getNumberOfParameters()) {
            throw new LogicException(sprintf('Handler <%s> __invoke must have exactly one parameter.', $handler::class));
        }

        $type = $method->getParameters()[0]->getType();
        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            throw new LogicException(sprintf('Handler <%s> __invoke must type-hint a command class.', $handler::class));
        }

        $commandClass = $type->getName();
        if (!is_a($commandClass, Command::class, true)) {
            throw new LogicException(sprintf('Handler <%s> must handle a Command, got <%s>.', $handler::class, $commandClass));
        }

        return $commandClass;
    }

    /** @return callable(Command): void */
    private function asInvoker(object $handler): callable
    {
        return static function (Command $command) use ($handler): void {
            $handler->__invoke($command); // @phpstan-ignore method.notFound
        };
    }
}
