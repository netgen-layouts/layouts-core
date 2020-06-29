<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\Transfer\Output\EntityLoader\Stubs\EntityLoaderStub;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Descriptor;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\Serializer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class SerializerTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Transfer\Output\Serializer
     */
    private $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(['entity' => new EntityLoaderStub()])
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::__construct
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::getEntityLoader
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serialize
     */
    public function testSerialize(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    ['visited_value'],
                    ['visited_value'],
                ],
            ],
            $this->serializer->serialize('entity', [$uuid1->toString(), $uuid2->toString()])
        );
    }
}
