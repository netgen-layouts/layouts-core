<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TargetParamConverterTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private TargetParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new TargetParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['targetId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('target', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Target::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $target = new Target();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTarget')
            ->with(self::equalTo($uuid))
            ->willReturn($target);

        self::assertSame(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\TargetParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $target = new Target();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadTargetDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($target);

        self::assertSame(
            $target,
            $this->paramConverter->loadValue(
                [
                    'targetId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
