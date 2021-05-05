<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\Exception\Transfer\TransferException;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Tests\Transfer\Stubs\EntityHandlerStub;
use Netgen\Layouts\Transfer\Descriptor;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\Serializer;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

final class SerializerTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(['entity' => new EntityHandlerStub()]),
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::__construct
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::getEntityHandler
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
                    ['visited_key' => 'visited_value'],
                    ['visited_key' => 'visited_value'],
                ],
            ],
            $this->serializer->serialize([$uuid1->toString() => 'entity', $uuid2->toString() => 'entity']),
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::getEntityHandler
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serialize
     */
    public function testSerializeWithNoHandler(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Entity handler for "entity" entity type does not exist.');

        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(),
        );

        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->serializer->serialize([$uuid1->toString() => 'entity', $uuid2->toString() => 'entity']);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::getEntityHandler
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serialize
     */
    public function testSerializeWithInvalidHandler(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Entity handler for "entity" entity type does not exist.');

        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(['entity' => new stdClass()]),
        );

        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $this->serializer->serialize([$uuid1->toString() => 'entity', $uuid2->toString() => 'entity']);
    }
}
