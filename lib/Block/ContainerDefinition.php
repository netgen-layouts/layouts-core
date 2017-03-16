<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\Exception\InvalidArgumentException;

class ContainerDefinition extends BlockDefinition implements ContainerDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    protected $placeholders = array();

    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    protected $dynamicPlaceholder;

    /**
     * Returns placeholder definitions.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface[]
     */
    public function getPlaceholders()
    {
        if ($this->isDynamicContainer()) {
            return array();
        }

        return $this->placeholders;
    }

    /**
     * Returns a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException if the placeholder does not exist
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getPlaceholder($placeholderIdentifier)
    {
        $exceptionMessage = null;
        if ($this->isDynamicContainer()) {
            $exceptionMessage = 'Container definition is a dynamic container and does not have any placeholders.';
        } elseif (!$this->hasPlaceholder($placeholderIdentifier)) {
            $exceptionMessage = sprintf(
                'Placeholder with "%s" identifier does not exist in block definition.',
                $placeholderIdentifier
            );
        }

        if ($exceptionMessage !== null) {
            throw new InvalidArgumentException('placeholderIdentifier', $exceptionMessage);
        }

        return $this->placeholders[$placeholderIdentifier];
    }

    /**
     * Returns if block definition has a placeholder definition.
     *
     * @param string $placeholderIdentifier
     *
     * @return bool
     */
    public function hasPlaceholder($placeholderIdentifier)
    {
        if ($this->isDynamicContainer()) {
            return false;
        }

        return isset($this->placeholders[$placeholderIdentifier]);
    }

    /**
     * Returns dynamic placeholder definition.
     *
     * @return \Netgen\BlockManager\Block\PlaceholderDefinitionInterface
     */
    public function getDynamicPlaceholder()
    {
        if (!$this->isDynamicContainer()) {
            return null;
        }

        return $this->dynamicPlaceholder;
    }

    /**
     * Returns if this block definition is a dynamic container.
     *
     * @return bool
     */
    public function isDynamicContainer()
    {
        return $this->handler->isDynamicContainer() && empty($this->placeholders);
    }
}
