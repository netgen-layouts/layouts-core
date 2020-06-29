<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output\EntityLoader;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleEntityLoaderTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader
     */
    private $entityLoader;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->entityLoader = new RuleEntityLoader(
            $this->layoutResolverServiceMock
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader::__construct
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader::loadEntities
     */
    public function testLoadEntities(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $rule1 = Rule::fromArray(['id' => $uuid1]);
        $rule2 = Rule::fromArray(['id' => $uuid2]);

        $this->layoutResolverServiceMock
            ->method('loadRule')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls($rule1, $rule2);

        $entities = [];
        foreach ($this->entityLoader->loadEntities([$uuid1->toString(), $uuid2->toString()]) as $entity) {
            $entities[] = $entity;
        }

        self::assertSame([$rule1, $rule2], $entities);
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader::__construct
     * @covers \Netgen\Layouts\Transfer\Output\EntityLoader\RuleEntityLoader::loadEntities
     */
    public function testLoadEntitiesWithNonExistentEntity(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $rule = Rule::fromArray(['id' => $uuid2]);

        $this->layoutResolverServiceMock
            ->method('loadRule')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls(
                self::throwException(new NotFoundException('rule', $uuid1->toString())),
                self::returnValue($rule)
            );

        $entities = [];
        foreach ($this->entityLoader->loadEntities([$uuid1->toString(), $uuid2->toString()]) as $entity) {
            $entities[] = $entity;
        }

        self::assertSame([$rule], $entities);
    }
}
