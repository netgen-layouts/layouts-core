<?php

namespace Netgen\BlockManager\Tests\Browser\Item\Renderer;

use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Browser\Item\Renderer\LayoutTemplateValueProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTemplateValueProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\Renderer\LayoutTemplateValueProvider
     */
    protected $valueProvider;

    public function setUp()
    {
        $this->valueProvider = new LayoutTemplateValueProvider();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\Renderer\LayoutTemplateValueProvider::getValues
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
     * @return \Netgen\ContentBrowser\Item\ItemInterface
     */
    protected function getItem()
    {
        return new Item(new Layout());
    }
}
