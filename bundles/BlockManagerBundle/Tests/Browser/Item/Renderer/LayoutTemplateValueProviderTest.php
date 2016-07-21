<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Browser\Item\Renderer;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Renderer\LayoutTemplateValueProvider;
use PHPUnit\Framework\TestCase;

class LayoutTemplateValueProviderTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\Renderer\LayoutTemplateValueProvider
     */
    protected $valueProvider;

    public function setUp()
    {
        $this->valueProvider = new LayoutTemplateValueProvider();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Renderer\LayoutTemplateValueProvider::getValues
     */
    public function testGetValues()
    {
        $item = $this->getItem();

        $this->assertEquals(
            array(
                'layout' => $item->getLayout(),
            ),
            $this->valueProvider->getValues($item)
        );
    }

    /**
     * @return \Netgen\Bundle\ContentBrowserBundle\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new LayoutInfo());
    }
}
