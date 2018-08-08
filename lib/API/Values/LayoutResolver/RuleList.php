<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;

final class RuleList extends ArrayCollection
{
    public function __construct(array $rules = [])
    {
        parent::__construct(
            array_filter(
                $rules,
                function (Rule $rule) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function getRules(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getRuleIds(): array
    {
        return array_map(
            function (Rule $rule) {
                return $rule->getId();
            },
            $this->getRules()
        );
    }
}
