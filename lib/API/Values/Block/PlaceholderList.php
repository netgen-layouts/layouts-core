<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;

final class PlaceholderList extends ArrayCollection
{
    public function __construct(array $placeholders = [])
    {
        parent::__construct(
            array_filter(
                $placeholders,
                function (Placeholder $placeholder) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Block\Placeholder[]
     */
    public function getPlaceholders(): array
    {
        return $this->toArray();
    }

    /**
     * @return string[]
     */
    public function getPlaceholderIdentifiers(): array
    {
        return array_map(
            function (Placeholder $placeholder) {
                return $placeholder->getIdentifier();
            },
            $this->getPlaceholders()
        );
    }
}
