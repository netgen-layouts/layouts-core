<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\StructBuilder;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;

final class LayoutResolverStructBuilder
{
    /**
     * Creates a new rule create struct.
     */
    public function newRuleCreateStruct(): RuleCreateStruct
    {
        return new RuleCreateStruct();
    }

    /**
     * Creates a new rule update struct.
     */
    public function newRuleUpdateStruct(): RuleUpdateStruct
    {
        return new RuleUpdateStruct();
    }

    /**
     * Creates a new rule metadata update struct.
     */
    public function newRuleMetadataUpdateStruct(): RuleMetadataUpdateStruct
    {
        return new RuleMetadataUpdateStruct();
    }

    /**
     * Creates a new rule group create struct.
     */
    public function newRuleGroupCreateStruct(string $name): RuleGroupCreateStruct
    {
        $struct = new RuleGroupCreateStruct();
        $struct->name = $name;

        return $struct;
    }

    /**
     * Creates a new rule group update struct.
     */
    public function newRuleGroupUpdateStruct(): RuleGroupUpdateStruct
    {
        return new RuleGroupUpdateStruct();
    }

    /**
     * Creates a new rule group metadata update struct.
     */
    public function newRuleGroupMetadataUpdateStruct(): RuleGroupMetadataUpdateStruct
    {
        return new RuleGroupMetadataUpdateStruct();
    }

    /**
     * Creates a new target create struct from the provided values.
     */
    public function newTargetCreateStruct(string $type): TargetCreateStruct
    {
        $struct = new TargetCreateStruct();
        $struct->type = $type;

        return $struct;
    }

    /**
     * Creates a new target update struct.
     */
    public function newTargetUpdateStruct(): TargetUpdateStruct
    {
        return new TargetUpdateStruct();
    }

    /**
     * Creates a new condition create struct from the provided values.
     */
    public function newConditionCreateStruct(string $type): ConditionCreateStruct
    {
        $struct = new ConditionCreateStruct();
        $struct->type = $type;

        return $struct;
    }

    /**
     * Creates a new condition update struct.
     */
    public function newConditionUpdateStruct(): ConditionUpdateStruct
    {
        return new ConditionUpdateStruct();
    }
}
