<?php

declare(strict_types=1);

namespace App\Tests\Controller\Type;

use App\Catalog\Type\Domain\TypeId;
use App\Tests\Catalog\Type\Application\Create\CreateTypeCommandMother;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TypePutControllerTest extends WebTestCase
{
    public function testCreatesType(): void
    {
        $id = TypeId::random()->value();
        $command = CreateTypeCommandMother::create(id: $id, code: 'book-' . substr($id, 0, 8));
        $client = self::createClient();

        $client->jsonRequest('PUT', '/types/' . $id, [
            'code' => $command->code(),
            'title' => $command->title(),
        ]);
        self::assertResponseStatusCodeSame(201);
    }
}
