<?php

namespace Netgen\BlockManager\Tests\Parameters\Value;

use Netgen\BlockManager\Parameters\Value\LinkValue;
use PHPUnit\Framework\TestCase;

final class LinkValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLink
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkSuffix
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkType
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getNewWindow
     */
    public function testSetDefaultProperties()
    {
        $linkValue = new LinkValue();

        $this->assertNull($linkValue->getLinkType());
        $this->assertNull($linkValue->getLink());
        $this->assertNull($linkValue->getLinkSuffix());
        $this->assertFalse($linkValue->getNewWindow());
    }

    /**
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLink
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkSuffix
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getLinkType
     * @covers \Netgen\BlockManager\Parameters\Value\LinkValue::getNewWindow
     */
    public function testSetProperties()
    {
        $linkValue = new LinkValue(
            array(
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'mail@example.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            )
        );

        $this->assertEquals(LinkValue::LINK_TYPE_EMAIL, $linkValue->getLinkType());
        $this->assertEquals('mail@example.com', $linkValue->getLink());
        $this->assertEquals('?suffix', $linkValue->getLinkSuffix());
        $this->assertTrue($linkValue->getNewWindow());
    }
}
