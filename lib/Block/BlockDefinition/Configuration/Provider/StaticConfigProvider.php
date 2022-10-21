<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\BlockDefinition\Configuration\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ConfigProviderInterface;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Exception\RuntimeException;

use function count;
use function is_array;
use function sprintf;

final class StaticConfigProvider implements ConfigProviderInterface
{
    private string $blockDefinitionIdentifier;

    /**
     * @var array<string, mixed>
     */
    private array $config;

    /**
     * @var array<string, mixed>
     */
    private array $viewTypes;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(string $blockDefinitionIdentifier, array $config)
    {
        $this->blockDefinitionIdentifier = $blockDefinitionIdentifier;
        $this->config = $config;
    }

    public function provideViewTypes(?Block $block = null): array
    {
        $this->viewTypes ??= $this->processViewTypes();

        return $this->viewTypes;
    }

    /**
     * @return \Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType[]
     */
    private function processViewTypes(): array
    {
        $viewTypes = [];

        foreach (($this->config['view_types'] ?? []) as $viewTypeIdentifier => $viewTypeConfig) {
            if ($viewTypeConfig['enabled'] === false) {
                continue;
            }

            $itemViewTypes = [];

            if (!is_array($viewTypeConfig['item_view_types'] ?? [])) {
                $viewTypeConfig['item_view_types'] = [];
            }

            $viewTypeConfig['item_view_types']['standard'] ??= [
                'name' => 'Standard',
                'enabled' => true,
            ];

            foreach ($viewTypeConfig['item_view_types'] as $itemViewTypeIdentifier => $itemViewTypeConfig) {
                if ($itemViewTypeConfig['enabled'] === false) {
                    continue;
                }

                $itemViewTypes[$itemViewTypeIdentifier] = ItemViewType::fromArray(
                    [
                        'identifier' => $itemViewTypeIdentifier,
                        'name' => $itemViewTypeConfig['name'],
                    ],
                );
            }

            if (count($itemViewTypes) === 0) {
                throw new RuntimeException(
                    sprintf(
                        'You need to specify at least one enabled item view type for "%s" view type and "%s" block definition.',
                        $viewTypeIdentifier,
                        $this->blockDefinitionIdentifier,
                    ),
                );
            }

            $viewTypes[$viewTypeIdentifier] = ViewType::fromArray(
                [
                    'identifier' => $viewTypeIdentifier,
                    'name' => $viewTypeConfig['name'] ?? '',
                    'itemViewTypes' => $itemViewTypes,
                    'validParameters' => $viewTypeConfig['valid_parameters'] ?? null,
                ],
            );
        }

        if (count($viewTypes) === 0) {
            throw new RuntimeException(
                sprintf(
                    'You need to specify at least one enabled view type for "%s" block definition.',
                    $this->blockDefinitionIdentifier,
                ),
            );
        }

        return $viewTypes;
    }
}
