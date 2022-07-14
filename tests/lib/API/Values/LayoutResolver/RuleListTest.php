<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class RuleListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Rule::class),
                stdClass::class,
            ),
        );

        new RuleList([new Rule(), new stdClass(), new Rule()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleList::__construct
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleList::getRules
     */
    public function testGetRules(): void
    {
        $rules = [new Rule(), new Rule()];

        self::assertSame($rules, (new RuleList($rules))->getRules());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleList::getRuleIds
     */
    public function testGetRuleIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $rules = [Rule::fromArray(['id' => $uuid1]), Rule::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new RuleList($rules))->getRuleIds());
    }
}
