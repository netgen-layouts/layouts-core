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
                    'title' => 'Title',
                    'use_link' => null,
                    'link' => new LinkValue(),
                    'css_class' => null,
                    'css_id' => null,
                    'set_container' => null,
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                ),
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                    'use_link' => null,
                    'link' => new LinkValue(),
                    'css_class' => null,
                    'css_id' => null,
                    'set_container' => null,
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                    'use_link' => true,
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_URL,
                            'link' => 'http://www.netgenlabs.com',
                        )
                    ),
                    'css_class' => 'some-class',
                    'css_id' => 'css-id',
                    'set_container' => true,
                ),
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                    'use_link' => true,
                    'link' => new LinkValue(
                        array(
                            'linkType' => LinkValue::LINK_TYPE_URL,
                            'link' => 'http://www.netgenlabs.com',
                        )
                    ),
                    'css_class' => 'some-class',
                    'css_id' => 'css-id',
                    'set_container' => true,
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
                    'title' => 'New title',
                ),
            ),
            array(
                array(
                    'tag' => '',
                    'title' => 'New title',
                ),
            ),
            array(
                array(
                    'tag' => 42,
                    'title' => 'New title',
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => null,
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => '',
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => 42,
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                    'use_link' => 42,
                ),
            ),
            array(
                array(
                    'tag' => 'h2',
                    'title' => 'New title',
                    'use_link' => true,
                    'link' => 42,
                ),
            ),
        );
    }
}
