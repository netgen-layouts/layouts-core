<?php

namespace Netgen\BlockManager\Collection\Item\VisibilityResolver;

use DateTimeImmutable;
use DateTimeInterface;
use Netgen\BlockManager\API\Values\Collection\Item;

final class ConfiguredVoter implements VoterInterface
{
    public function vote(Item $item)
    {
        $visibilityConfig = $item->getConfig('visibility');
        if (!$visibilityConfig->getParameter('visible')->getValue()) {
            return self::NO;
        }

        $currentDate = new DateTimeImmutable();

        $visibleFrom = $visibilityConfig->getParameter('visible_from')->getValue();
        $visibleTo = $visibilityConfig->getParameter('visible_to')->getValue();

        if ($visibleFrom instanceof DateTimeInterface && $currentDate < $visibleFrom) {
            return self::NO;
        }

        if ($visibleTo instanceof DateTimeInterface && $currentDate > $visibleTo) {
            return self::NO;
        }

        return self::ABSTAIN;
    }
}
