<?php

namespace Netgen\BlockManager\Tests\Core\Service\Block;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\BlockManager\Parameters\Value\LinkValue;

abstract class TitleTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new TitleHandler(
            array(
                'h1' => 'Heading 1',
                'h2' => 'Heading 2',
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
                    'tag' => 'h1',
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                ),
                array(
                    'tag' => 'h2',
                ),
            ),
            array(
                array(),
                array(
                    'title' => 'Title',
                ),
            ),
            array(
                array(
                    'title' => 'New title',
                ),
                array(
                    'title' => 'New title',
                ),
            ),
            array(
                array(),
                array(
                    'use_link' => null,
                    'link' => new LinkValue(),
                ),
            ),
            array(
                array(
                    'use_link' => true,
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_URL,
                            'link' => 'http://www.netgenlabs.com',
                        )
                    ),
                ),
                array(
                    'use_link' => true,
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
                    'use_link' => true,
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_INTERNAL,
                            'link' => 'value://42',
                        )
                    ),
                ),
                array(
                    'use_link' => true,
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_INTERNAL,
                            'link' => 'value://42',
                        )
                    ),
                ),
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
                    'tag' => null,
                ),
            ),
            array(
                array(
                    'tag' => '',
                ),
            ),
            array(
                array(
                    'tag' => 42,
                ),
            ),
            array(
                array(
                    'title' => null,
                ),
            ),
            array(
                array(
                    'title' => '',
                ),
            ),
            array(
                array(
                    'title' => 42,
                ),
            ),
            array(
                array(
                    'use_link' => 42,
                ),
            ),
            array(
                array(
                    'link' => 42,
                ),
                array('use_link', 'link'),
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
                array('use_link', 'link'),
            ),
        );
    }
}
