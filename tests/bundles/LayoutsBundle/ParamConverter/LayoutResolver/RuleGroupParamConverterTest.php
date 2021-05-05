<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleGroupParamConverterTest extends TestCase
{
    private MockObject $layoutResolverServiceMock;

    private RuleGroupParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleGroupParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleGroupId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('ruleGroup', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(RuleGroup::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroup')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->paramConverter->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::loadValue
     */
    public function testLoadValueArchive(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->paramConverter->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'archived',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleGroupParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $ruleGroup = new RuleGroup();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleGroupDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($ruleGroup);

        self::assertSame(
            $ruleGroup,
            $this->paramConverter->loadValue(
                [
                    'ruleGroupId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
