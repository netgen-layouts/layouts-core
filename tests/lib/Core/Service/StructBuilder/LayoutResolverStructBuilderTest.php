<?php

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

abstract class LayoutResolverStructBuilderTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder
     */
    protected $structBuilder;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->structBuilder = new LayoutResolverStructBuilder();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct()
    {
        $this->assertEquals(
            new RuleCreateStruct(),
            $this->structBuilder->newRuleCreateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct()
    {
        $this->assertEquals(
            new RuleUpdateStruct(),
            $this->structBuilder->newRuleUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct()
    {
        $this->assertEquals(
            new RuleMetadataUpdateStruct(),
            $this->structBuilder->newRuleMetadataUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct()
    {
        $createStruct = $this->structBuilder->newTargetCreateStruct('target');
        $createStruct->value = '42';

        $this->assertEquals(
            new TargetCreateStruct(
                array(
                    'type' => 'target',
                    'value' => '42',
                )
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct()
    {
        $updateStruct = $this->structBuilder->newTargetUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new TargetUpdateStruct(
                array(
                    'value' => '42',
                )
            ),
            $updateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct()
    {
        $createStruct = $this->structBuilder->newConditionCreateStruct('condition');
        $createStruct->value = 42;

        $this->assertEquals(
            new ConditionCreateStruct(
                array(
                    'type' => 'condition',
                    'value' => '42',
                )
            ),
            $createStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\StructBuilder\LayoutResolverStructBuilder::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct()
    {
        $updateStruct = $this->structBuilder->newConditionUpdateStruct();
        $updateStruct->value = '42';

        $this->assertEquals(
            new ConditionUpdateStruct(
                array(
                    'value' => '42',
                )
            ),
            $updateStruct
        );
    }
}
