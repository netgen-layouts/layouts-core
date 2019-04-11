<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;

final class PlaceholderList extends ArrayCollection
{
    public function __construct(array $placeholders = [])
    {
        parent::__construct(
            array_filter(
                $placeholders,
                static function (Placeholder $placeholder): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\Block\Placeholder[]
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
            static function (Placeholder $placeholder): string {
                return $placeholder->getIdentifier();
            },
            $this->getPlaceholders()
        );
    }
}
