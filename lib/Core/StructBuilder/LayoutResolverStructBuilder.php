<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\StructBuilder;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;

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
