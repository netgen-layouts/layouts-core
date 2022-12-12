<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Stubs;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;

final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var array<string, \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType>
     */
    private array $viewTypes;

    /**
     * @param array<string, \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType> $viewTypes
     */
    private function __construct(array $viewTypes)
    {
        $this->viewTypes = $viewTypes;
    }

    /**
     * @param array<string, string[]> $viewTypes
     */
    public static function fromShortConfig(array $viewTypes = []): self
    {
        $viewTypesList = [];

        foreach ($viewTypes as $viewType => $itemViewTypes) {
            $itemViewTypesList = [];

            foreach ($itemViewTypes as $itemViewType) {
                $itemViewTypesList[$itemViewType] = new ItemViewType();
            }

            $viewTypesList[$viewType] = ViewType::fromArray(
                [
                    'itemViewTypes' => $itemViewTypesList,
                ],
            );
        }

        return new self($viewTypesList);
    }

    /**
     * @param array<string, \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType> $viewTypes
     */
    public static function fromFullConfig(array $viewTypes): self
    {
        return new self($viewTypes);
    }

    public function provideViewTypes(?Block $block = null): array
    {
        return $this->viewTypes;
    }
}
