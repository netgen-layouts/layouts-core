<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\Exception\Transfer\TransferException;
use Netgen\Layouts\Tests\Stubs\Container;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Tests\Transfer\Stubs\EntityHandlerStub;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\Serializer;
use Netgen\Layouts\Transfer\Output\SerializerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Serializer::class)]
final class SerializerTest extends TestCase
{
    private Serializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(['layout' => new EntityHandlerStub()]),
        );
    }

    public function testSerialize(): void
    {
        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        self::assertSame(
            [
                '__version' => SerializerInterface::FORMAT_VERSION,
                'layouts' => [
                    ['visited_key' => 'visited_value'],
                    ['visited_key' => 'visited_value'],
                ],
            ],
            $this->serializer->serialize([$uuid1->toString() => 'layout', $uuid2->toString() => 'layout']),
        );
    }

    public function testSerializeWithNoHandler(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Entity handler for "layout" entity type does not exist.');

        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(),
        );

        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $this->serializer->serialize([$uuid1->toString() => 'layout', $uuid2->toString() => 'layout']);
    }

    public function testSerializeWithInvalidHandler(): void
    {
        $this->expectException(TransferException::class);
        $this->expectExceptionMessage('Entity handler for "layout" entity type does not exist.');

        $this->serializer = new Serializer(
            new OutputVisitor([new VisitorStub()]),
            new Container(['entity' => new stdClass()]),
        );

        $uuid1 = Uuid::v7();
        $uuid2 = Uuid::v7();

        $this->serializer->serialize([$uuid1->toString() => 'layout', $uuid2->toString() => 'layout']);
    }
}
