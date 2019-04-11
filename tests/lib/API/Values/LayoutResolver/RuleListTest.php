<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class RuleListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                RuleList::class,
                str_replace('\RuleList', '', RuleList::class),
                Rule::class,
                stdClass::class
            )
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
        $rules = [Rule::fromArray(['id' => 42]), Rule::fromArray(['id' => 24])];

        self::assertSame([42, 24], (new RuleList($rules))->getRuleIds());
    }
}
