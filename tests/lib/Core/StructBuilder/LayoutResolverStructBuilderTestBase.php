<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;

abstract class LayoutResolverStructBuilderTestBase extends CoreTestCase
{
    use ExportObjectTrait;

    private LayoutResolverStructBuilder $structBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->structBuilder = new LayoutResolverStructBuilder();
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleCreateStruct
     */
    public function testNewRuleCreateStruct(): void
    {
        $struct = $this->structBuilder->newRuleCreateStruct();

        self::assertSame(
            [
                'comment' => '',
                'description' => '',
                'enabled' => true,
                'layoutId' => null,
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleUpdateStruct
     */
    public function testNewRuleUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleUpdateStruct();

        self::assertSame(
            [
                'comment' => null,
                'description' => null,
                'layoutId' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleMetadataUpdateStruct
     */
    public function testNewRuleMetadataUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleMetadataUpdateStruct();

        self::assertSame(
            [
                'priority' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleGroupCreateStruct
     */
    public function testNewRuleGroupCreateStruct(): void
    {
        $struct = $this->structBuilder->newRuleGroupCreateStruct('Test group');

        self::assertSame(
            [
                'description' => '',
                'enabled' => true,
                'name' => 'Test group',
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleGroupUpdateStruct
     */
    public function testNewRuleGroupUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleGroupUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'name' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newRuleGroupMetadataUpdateStruct
     */
    public function testNewRuleGroupMetadataUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleGroupMetadataUpdateStruct();

        self::assertSame(
            [
                'priority' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newTargetCreateStruct
     */
    public function testNewTargetCreateStruct(): void
    {
        $struct = $this->structBuilder->newTargetCreateStruct('target');

        self::assertSame(
            [
                'type' => 'target',
                'value' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newTargetUpdateStruct
     */
    public function testNewTargetUpdateStruct(): void
    {
        $struct = $this->structBuilder->newTargetUpdateStruct();

        self::assertSame(
            [
                'value' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newConditionCreateStruct
     */
    public function testNewConditionCreateStruct(): void
    {
        $struct = $this->structBuilder->newConditionCreateStruct('condition');

        self::assertSame(
            [
                'type' => 'condition',
                'value' => null,
            ],
            $this->exportObject($struct),
        );
    }

    /**
     * @covers \Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder::newConditionUpdateStruct
     */
    public function testNewConditionUpdateStruct(): void
    {
        $struct = $this->structBuilder->newConditionUpdateStruct();

        self::assertSame(
            [
                'value' => null,
            ],
            $this->exportObject($struct),
        );
    }
}
