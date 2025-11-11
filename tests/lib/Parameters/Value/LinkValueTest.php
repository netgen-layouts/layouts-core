<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\Value;

use Netgen\Layouts\Parameters\Value\LinkType;
use Netgen\Layouts\Parameters\Value\LinkValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LinkValue::class)]
final class LinkValueTest extends TestCase
{
    public function testSetProperties(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkType::Email,
                'link' => 'info@netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        self::assertSame(LinkType::Email, $linkValue->getLinkType());
        self::assertSame('info@netgen.io', $linkValue->getLink());
        self::assertSame('?suffix', $linkValue->getLinkSuffix());
        self::assertTrue($linkValue->getNewWindow());
    }

    public function testToString(): void
    {
        $linkValue = LinkValue::fromArray(
            [
                'linkType' => LinkType::Email,
                'link' => 'info@netgen.io',
                'linkSuffix' => '?suffix',
                'newWindow' => true,
            ],
        );

        self::assertSame('info@netgen.io?suffix', (string) $linkValue);
    }
}
