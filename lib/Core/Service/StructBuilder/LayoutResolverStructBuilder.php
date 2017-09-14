<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;

class LayoutResolverStructBuilder
{
    /**
     * Creates a new rule create struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct
     */
    public function newRuleCreateStruct()
    {
        return new RuleCreateStruct();
    }

    /**
     * Creates a new rule update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct
     */
    public function newRuleUpdateStruct()
    {
        return new RuleUpdateStruct();
    }

    /**
     * Creates a new rule metadata update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct
     */
    public function newRuleMetadataUpdateStruct()
    {
        return new RuleMetadataUpdateStruct();
    }

    /**
     * Creates a new target create struct from the provided values.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct
     */
    public function newTargetCreateStruct($type)
    {
        return new TargetCreateStruct(
            array(
                'type' => $type,
            )
        );
    }

    /**
     * Creates a new target update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct
     */
    public function newTargetUpdateStruct()
    {
        return new TargetUpdateStruct();
    }

    /**
     * Creates a new condition create struct from the provided values.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct
     */
    public function newConditionCreateStruct($type)
    {
        return new ConditionCreateStruct(
            array(
                'type' => $type,
            )
        );
    }

    /**
     * Creates a new condition update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct()
    {
        return new ConditionUpdateStruct();
    }
}
