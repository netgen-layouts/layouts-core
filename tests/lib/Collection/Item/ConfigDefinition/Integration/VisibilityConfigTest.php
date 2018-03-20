<?php

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration;

use DateTimeImmutable;
use DateTimeZone;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\ConfigDefinition\Handler\VisibilityConfigHandler;

abstract class VisibilityConfigTest extends ItemTest
{
    /**
     * @return \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    public function createConfigDefinitionHandler()
    {
        return new VisibilityConfigHandler();
    }

    /**
     * @return array
     */
    public function configDataProvider()
    {
        $dateFrom = new DateTimeImmutable('2018-01-02 15:00:00', new DateTimeZone('Antarctica/Casey'));
        $dateTo = new DateTimeImmutable('2018-01-02 16:00:00', new DateTimeZone('Antarctica/Casey'));

        return array(
            array(
                array(),
                array(
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => null,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function invalidConfigDataProvider()
    {
        return array(
            array(
                array(
                    'visibility_status' => 42,
                ),
            ),
            array(
                array(
                    'visible_from' => 42,
                ),
            ),
            array(
                array(
                    'visible_to' => 42,
                ),
            ),
        );
    }
}
