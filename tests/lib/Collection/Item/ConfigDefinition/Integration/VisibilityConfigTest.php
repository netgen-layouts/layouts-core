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

        $dateFrom2 = new DateTimeImmutable('2018-01-02 13:00:00', new DateTimeZone('Europe/Zagreb'));
        $dateTo2 = new DateTimeImmutable('2018-01-02 12:01:00', new DateTimeZone('Europe/London'));

        return [
            [
                [],
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => null,
                    'visible_to' => null,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidConfigDataProvider()
    {
        $dateFrom = new DateTimeImmutable('2018-01-02 16:00:00', new DateTimeZone('Antarctica/Casey'));
        $dateTo = new DateTimeImmutable('2018-01-02 15:00:00', new DateTimeZone('Antarctica/Casey'));

        $dateFrom2 = new DateTimeImmutable('2018-01-02 15:00:00', new DateTimeZone('Europe/London'));
        $dateTo2 = new DateTimeImmutable('2018-01-02 15:59:00', new DateTimeZone('Europe/Zagreb'));

        return [
            [
                [
                    'visibility_status' => 42,
                ],
            ],
            [
                [
                    'visible_from' => 42,
                ],
            ],
            [
                [
                    'visible_to' => 42,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => 42,
                    'visible_to' => 42,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_VISIBLE,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_HIDDEN,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateFrom,
                ],
            ],
            [
                [
                    'visibility_status' => Item::VISIBILITY_SCHEDULED,
                    'visible_from' => $dateFrom2,
                    'visible_to' => $dateTo2,
                ],
            ],
        ];
    }
}
