<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\GalleryHandler;

abstract class GalleryTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new GalleryHandler(
            3,
            7,
            array(
                'horizontal' => 'Horizontal',
                'vertical' => 'Vertical',
            ),
            array(
                'slide' => 'Slide',
                'fade' => 'Fade',
            ),
            array(
                '16_9' => '16:9',
                '4_3' => '4:3',
            )
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
                    'next_and_previous' => null,
                ),
            ),
            array(
                array(
                    'next_and_previous' => false,
                ),
                array(
                    'next_and_previous' => false,
                ),
            ),
            array(
                array(
                    'show_pagination' => true,
                ),
                array(
                    'show_pagination' => true,
                    'pagination_type' => 'horizontal',
                ),
            ),
            array(
                array(
                    'show_pagination' => true,
                    'pagination_type' => 'vertical',
                ),
                array(
                    'show_pagination' => true,
                    'pagination_type' => 'vertical',
                ),
            ),
            array(
                array(),
                array(
                    'infinite_loop' => null,
                ),
            ),
            array(
                array(
                    'infinite_loop' => false,
                ),
                array(
                    'infinite_loop' => false,
                ),
            ),
            array(
                array(),
                array(
                    'transition' => 'slide',
                ),
            ),
            array(
                array(
                    'transition' => 'fade',
                ),
                array(
                    'transition' => 'fade',
                ),
            ),
            array(
                array(
                    'autoplay' => true,
                ),
                array(
                    'autoplay' => true,
                    'autoplay_time' => 3,
                ),
            ),
            array(
                array(
                    'autoplay' => true,
                    'autoplay_time' => 5,
                ),
                array(
                    'autoplay' => true,
                    'autoplay_time' => 5,
                ),
            ),
            array(
                array(),
                array(
                    'aspect_ratio' => '16_9',
                ),
            ),
            array(
                array(
                    'aspect_ratio' => '4_3',
                ),
                array(
                    'aspect_ratio' => '4_3',
                ),
            ),
            array(
                array(),
                array(
                    'number_of_thumbnails' => 1,
                ),
            ),
            array(
                array(
                    'number_of_thumbnails' => 16,
                ),
                array(
                    'number_of_thumbnails' => 16,
                ),
            ),
            array(
                array(
                    'show_details' => true,
                ),
                array(
                    'show_details' => true,
                    'show_details_on_hover' => null,
                ),
            ),
            array(
                array(
                    'show_details' => true,
                    'show_details_on_hover' => false,
                ),
                array(
                    'show_details' => true,
                    'show_details_on_hover' => false,
                ),
            ),
            array(
                array(),
                array(
                    'enable_lightbox' => null,
                ),
            ),
            array(
                array(
                    'enable_lightbox' => false,
                ),
                array(
                    'enable_lightbox' => false,
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
                    'next_and_previous' => 42,
                ),
            ),
            array(
                array(
                    'show_pagination' => 42,
                ),
            ),
            array(
                array(
                    'show_pagination' => true,
                    'pagination_type' => 'unknown',
                ),
            ),
            array(
                array(
                    'infinite_loop' => 42,
                ),
            ),
            array(
                array(
                    'transition' => null,
                ),
            ),
            array(
                array(
                    'transition' => 'unknown',
                ),
            ),
            array(
                array(
                    'autoplay' => 42,
                ),
            ),
            array(
                array(
                    'autoplay' => true,
                    'autoplay_time' => 15,
                ),
            ),
            array(
                array(
                    'autoplay' => true,
                    'autoplay_time' => '15',
                ),
            ),
            array(
                array(
                    'aspect_ratio' => null,
                ),
            ),
            array(
                array(
                    'aspect_ratio' => 'unknown',
                ),
            ),
            array(
                array(
                    'number_of_thumbnails' => null,
                ),
            ),
            array(
                array(
                    'number_of_thumbnails' => 0,
                ),
            ),
            array(
                array(
                    'number_of_thumbnails' => '16',
                ),
            ),
            array(
                array(
                    'show_details' => 42,
                ),
            ),
            array(
                array(
                    'show_details' => true,
                    'show_details_on_hover' => 42,
                ),
            ),
            array(
                array(
                    'enable_lightbox' => 42,
                ),
            ),
        );
    }
}
