<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\APIVersion;
use PHPUnit\Framework\TestCase;

final class APIVersionTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new APIVersion();
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\APIVersion::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $view = new View(new Value());
        $view->addParameter('api_version', 42);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [[24], false],
            [[42], true],
            [[43, 24], false],
            [[24, 42], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\APIVersion::match
     */
    public function testMatchWithNoAPIVersion(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
