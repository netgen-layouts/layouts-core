<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class VisibilityResolver implements VisibilityResolverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface[]
     */
    private $voters = array();

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets the available voters.
     *
     * @param \Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface[] $voters
     */
    public function setVoters(array $voters = array())
    {
        foreach ($voters as $voter) {
            if (!$voter instanceof VoterInterface) {
                throw new InvalidInterfaceException(
                    'Voter',
                    get_class($voter),
                    VoterInterface::class
                );
            }
        }

        $this->voters = $voters;
    }

    public function isVisible(Item $item)
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($item);
            if ($result !== VoterInterface::ABSTAIN) {
                if ($result === VoterInterface::NO) {
                    $this->logger->info(
                        sprintf(
                            'Collection item #%s (type: %s, value: %s) was declared as hidden by "%s" voter',
                            $item->getId(),
                            $item->getValueType(),
                            $item->getValue(),
                            get_class($voter)
                        )
                    );
                }

                return (bool) $result;
            }
        }

        return true;
    }
}
