<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use DateTime;
use Netgen\BlockManager\Persistence\Handler;

abstract class Mapper
{
    /**
     * @var \Netgen\BlockManager\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Handler $persistenceHandler
     */
    public function __construct(Handler $persistenceHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
    }

    /**
     * Returns \DateTime object from the timestamp.
     *
     * @param int $timestamp
     *
     * @return \DateTime
     */
    protected function createDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimestamp((int) $timestamp);

        return $dateTime;
    }
}
