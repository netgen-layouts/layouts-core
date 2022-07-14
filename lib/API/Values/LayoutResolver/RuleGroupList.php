<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup>
 */
final class RuleGroupList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup[] $ruleGroups
     */
    public function __construct(array $ruleGroups = [])
    {
        parent::__construct(
            array_filter(
                $ruleGroups,
                static fn (RuleGroup $ruleGroup): bool => true,
            ),
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\RuleGroup[]
     */
    public function getRuleGroups(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getRuleGroupIds(): array
    {
        return array_map(
            static fn (RuleGroup $ruleGroup): UuidInterface => $ruleGroup->getId(),
            $this->getRuleGroups(),
        );
    }
}
