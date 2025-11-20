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

    public function testNewRuleCreateStruct(): void
    {
        $struct = $this->structBuilder->newRuleCreateStruct();

        self::assertSame(
            [
                'description' => '',
                'enabled' => true,
                'layoutId' => null,
                'priority' => null,
                'uuid' => null,
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewRuleUpdateStruct(): void
    {
        $struct = $this->structBuilder->newRuleUpdateStruct();

        self::assertSame(
            [
                'description' => null,
                'layoutId' => null,
            ],
            $this->exportObject($struct),
        );
    }

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

    public function testNewTargetCreateStruct(): void
    {
        $struct = $this->structBuilder->newTargetCreateStruct('target');

        self::assertSame(
            [
                'type' => 'target',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewTargetUpdateStruct(): void
    {
        $struct = $this->structBuilder->newTargetUpdateStruct();

        self::assertSame([], $this->exportObject($struct));
    }

    public function testNewConditionCreateStruct(): void
    {
        $struct = $this->structBuilder->newConditionCreateStruct('condition');

        self::assertSame(
            [
                'type' => 'condition',
            ],
            $this->exportObject($struct),
        );
    }

    public function testNewConditionUpdateStruct(): void
    {
        $struct = $this->structBuilder->newConditionUpdateStruct();

        self::assertSame([], $this->exportObject($struct));
    }
}
