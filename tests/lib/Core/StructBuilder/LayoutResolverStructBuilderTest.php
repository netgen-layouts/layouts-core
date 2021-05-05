<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\StructBuilder;

use Netgen\Layouts\Core\StructBuilder\LayoutResolverStructBuilder;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Netgen\Layouts\Tests\TestCase\ExportObjectTrait;

abstract class LayoutResolverStructBuilderTest extends CoreTestCase
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
                'uuid' => null,
                'layoutId' => null,
                'priority' => null,
                'enabled' => true,
                'description' => '',
                'comment' => '',
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
                'layoutId' => null,
                'description' => null,
                'comment' => null,
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
                'uuid' => null,
                'name' => 'Test group',
                'description' => '',
                'priority' => null,
                'enabled' => true,
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
                'name' => null,
                'description' => null,
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
