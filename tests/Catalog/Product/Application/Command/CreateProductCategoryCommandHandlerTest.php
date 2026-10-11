<?php

declare(strict_types=1);

namespace App\Tests\Catalog\Product\Application\Command;

use PHPUnit\Framework\TestCase;

/**
 * TODO: отдельный тест здесь не нужен.
 *
 * CreateProductCategoryCommandHandler только собирает ProductId и CategoryId из строк команды
 * и передаёт их в ProductCategoryCreator. Веток и правил посадки в handler нет.
 * Невалидный UUID проверяют сами объекты значения.
 * Успех и отказы посадки проверяет ProductCategoryCreatorTest.
 */
final class CreateProductCategoryCommandHandlerTest extends TestCase
{
    public function testCreatesProductCategory(): void
    {
        self::markTestIncomplete('Смотри TODO у класса: проверять здесь нечего.');
    }
}
