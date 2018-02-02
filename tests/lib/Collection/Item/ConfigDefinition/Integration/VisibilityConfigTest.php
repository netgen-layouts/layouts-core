<?php

namespace Netgen\BlockManager\Tests\Collection\Item\ConfigDefinition\Integration;

use DateTimeImmutable;
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
        $dateFrom = DateTimeImmutable::createFromFormat(DateTimeImmutable::RFC3339, '2018-01-02T15:00:00+01:00');
        $dateTo = DateTimeImmutable::createFromFormat(DateTimeImmutable::RFC3339, '2018-01-02T16:00:00+01:00');

        return array(
            array(
                array(),
                array(
                    'visible' => true,
                    'visible_from' => null,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visible' => false,
                ),
                array(
                    'visible' => false,
                    'visible_from' => null,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visible' => false,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visible' => false,
                    'visible_from' => null,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visible' => false,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visible' => false,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
            ),
            array(
                array(
                    'visible' => true,
                ),
                array(
                    'visible' => true,
                    'visible_from' => null,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visible' => true,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ),
                array(
                    'visible' => true,
                    'visible_from' => $dateFrom,
                    'visible_to' => null,
                ),
            ),
            array(
                array(
                    'visible' => true,
                    'visible_from' => $dateFrom,
                    'visible_to' => $dateTo,
                ),
                array(
                    'visible' => true,
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
                    'visible' => 42,
                ),
            ),
            array(
                array(
                    'visible_from' => 42,
                ),
                array('visible', 'visible_to'),
            ),
            array(
                array(
                    'visible_to' => 42,
                ),
                array('visible', 'visible_from'),
            ),
        );
    }
}
