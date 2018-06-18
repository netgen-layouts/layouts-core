<?php

declare(strict_types=1);

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
    public function testSetProperties(): void
    {
        $linkValue = new LinkValue(
            [
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'mail@example.com',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ]
        );

        $this->assertSame(LinkValue::LINK_TYPE_EMAIL, $linkValue->getLinkType());
        $this->assertSame('mail@example.com', $linkValue->getLink());
        $this->assertSame('?suffix', $linkValue->getLinkSuffix());
        $this->assertTrue($linkValue->getNewWindow());
    }
}
