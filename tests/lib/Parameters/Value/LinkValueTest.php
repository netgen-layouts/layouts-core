<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Value;

use Netgen\Layouts\Parameters\Value\LinkValue;
use PHPUnit\Framework\TestCase;

final class LinkValueTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Parameters\Value\LinkValue::getLink
     * @covers \Netgen\Layouts\Parameters\Value\LinkValue::getLinkSuffix
     * @covers \Netgen\Layouts\Parameters\Value\LinkValue::getLinkType
     * @covers \Netgen\Layouts\Parameters\Value\LinkValue::getNewWindow
     */
    public function testSetProperties(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'info@netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        self::assertSame(LinkValue::LINK_TYPE_EMAIL, $linkValue->getLinkType());
        self::assertSame('info@netgen.io', $linkValue->getLink());
        self::assertSame('?suffix', $linkValue->getLinkSuffix());
        self::assertTrue($linkValue->getNewWindow());
    }

    /**
     * @covers \Netgen\Layouts\Parameters\Value\LinkValue::__toString
     */
    public function testToString(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkValue::LINK_TYPE_EMAIL,
                'link' => 'info@netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        self::assertSame('info@netgen.io?suffix', (string) $linkValue);
    }
}
