<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter;
use PHPUnit\Framework\TestCase;

final class RuleParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('rule', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Rule::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $rule = new Rule();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::identicalTo(42))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => 42,
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValueArchive(): void
    {
        $rule = new Rule();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleArchive')
            ->with(self::identicalTo(42))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => 42,
                    'status' => 'archived',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $rule = new Rule();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleDraft')
            ->with(self::identicalTo(42))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => 42,
                    'status' => 'draft',
                ]
            )
        );
    }
}
