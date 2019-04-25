<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\LayoutResolver;

use Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleParamConverterTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter
     */
    private $paramConverter;

    public function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->paramConverter = new RuleParamConverter($this->layoutResolverServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['ruleId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('rule', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Rule::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRule')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'published',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValueArchive(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleArchive')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'archived',
                ]
            )
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\LayoutResolver\RuleParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $rule = new Rule();

        $uuid = Uuid::uuid4();

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('loadRuleDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($rule);

        self::assertSame(
            $rule,
            $this->paramConverter->loadValue(
                [
                    'ruleId' => $uuid->toString(),
                    'status' => 'draft',
                ]
            )
        );
    }
}
