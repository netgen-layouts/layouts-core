<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LazyCollection;
use Symfony\Component\Uid\Uuid;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\LayoutResolver\Rule>
 */
final class RuleList extends LazyCollection
{
    /**
     * @return \Netgen\Layouts\API\Values\LayoutResolver\Rule[]
     */
    public function getRules(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Symfony\Component\Uid\Uuid[]
     */
    public function getRuleIds(): array
    {
        return array_map(
            static fn (Rule $rule): Uuid => $rule->id,
            $this->getRules(),
        );
    }
}
