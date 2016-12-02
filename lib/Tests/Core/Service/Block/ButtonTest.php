<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Handler\ButtonHandler;
use Netgen\BlockManager\Parameters\Value\LinkValue;

abstract class ButtonTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new ButtonHandler(
            array(
                'default_button' => 'Default button',
                'highlighted_button' => 'Highlighted button',
            ),
            array('value')
        );
    }

    /**
     * @return array
     */
    public function parametersDataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'text' => 'Text',
                ),
            ),
            array(
                array(
                    'text' => 'New text',
                ),
                array(
                    'text' => 'New text',
                ),
            ),
            array(
                array(),
                array(
                    'style' => 'default_button',
                ),
            ),
            array(
                array(
                    'style' => 'highlighted_button',
                ),
                array(
                    'style' => 'highlighted_button',
                ),
            ),
            array(
                array(),
                array(
                    'link' => new LinkValue(),
                ),
            ),
            array(
                array(
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_URL,
                            'link' => 'http://www.netgenlabs.com',
                        )
                    ),
                ),
                array(
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_URL,
                            'link' => 'http://www.netgenlabs.com',
                        )
                    ),
                ),
            ),
            array(
                array(
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_INTERNAL,
                            'link' => 'value://42',
                        )
                    ),
                ),
                array(
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_INTERNAL,
                            'link' => 'value://42',
                        )
                    ),
                ),
            ),
            array(
                array(
                    'unknown' => 'unknown',
                ),
                array(),
            ),
        );
    }

    /**
     * @return array
     */
    public function invalidParametersDataProvider()
    {
        return array(
            array(
                array(
                    'text' => null,
                ),
            ),
            array(
                array(
                    'text' => '',
                ),
            ),
            array(
                array(
                    'text' => 42,
                ),
            ),
            array(
                array(
                    'style' => null,
                ),
            ),
            array(
                array(
                    'style' => 42,
                ),
            ),
            array(
                array(
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_INTERNAL,
                            'link' => 'unknown://42',
                        )
                    ),
                ),
            ),
        );
    }
}
