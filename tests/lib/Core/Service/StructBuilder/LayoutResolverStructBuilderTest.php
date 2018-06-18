<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;
use Netgen\BlockManager\Tests\TestCase\ExportObjectVarsTrait;

abstract class LayoutResolverStructBuilderTest extends ServiceTestCase
{
    use ExportObjectVarsTrait;

    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder
     */
    private $structBuilder;

    public function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new LayoutResolverStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct(): void
    {
        $struct = $this->structBuilder->newRuleCreateStruct();

        $this->assertInstanceOf(RuleCreateStruct::class, $struct);

        $this->assertSame(
            [
                'layoutId' => null,
                'priority' => null,
                'enabled' => false,
                'comment' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleUpdateStruct();

        $this->assertInstanceOf(RuleUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'layoutId' => null,
                'comment' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleMetadataUpdateStruct();

        $this->assertInstanceOf(RuleMetadataUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'priority' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct(): void
    {
        $struct = $this->structBuilder->newTargetCreateStruct('target');

        $this->assertInstanceOf(TargetCreateStruct::class, $struct);

        $this->assertSame(
            [
                'type' => 'target',
                'value' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct(): void
    {
        $struct = $this->structBuilder->newTargetUpdateStruct();

        $this->assertInstanceOf(TargetUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'value' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct(): void
    {
        $struct = $this->structBuilder->newConditionCreateStruct('condition');

        $this->assertInstanceOf(ConditionCreateStruct::class, $struct);

        $this->assertSame(
            [
                'type' => 'condition',
                'value' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct(): void
    {
        $struct = $this->structBuilder->newConditionUpdateStruct();

        $this->assertInstanceOf(ConditionUpdateStruct::class, $struct);

        $this->assertSame(
            [
                'value' => null,
            ],
            $this->exportObjectVars($struct)
        );
    }
}
