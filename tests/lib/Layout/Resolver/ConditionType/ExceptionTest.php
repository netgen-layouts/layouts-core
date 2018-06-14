<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\ConditionType;

use Exception;
use Netgen\BlockManager\Layout\Resolver\ConditionType\Exception as ExceptionConditionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class ExceptionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception
     */
    private $conditionType;

    public function setUp(): void
    {
        $this->conditionType = new ExceptionConditionType();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::getType
     */
    public function testGetType(): void
    {
        $this->assertEquals('exception', $this->conditionType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');

        $request->attributes->set('exception', FlattenException::create(new Exception(), 404));

        $this->assertEquals($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithNoException(): void
    {
        $request = Request::create('/');

        $this->assertFalse($this->conditionType->matches($request, [404]));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithInvalidException(): void
    {
        $request = Request::create('/');

        $request->attributes->set('exception', new Exception());

        $this->assertFalse($this->conditionType->matches($request, [404]));
    }

    public function validationProvider(): array
    {
        return [
            [[404], true],
            [[404, 403], true],
            [[403, 700], false],
            [[403, 200], false],
            [[700], false],
            [[200], false],
            [[], true],
            [null, false],
        ];
    }

    public function matchesProvider(): array
    {
        return [
            ['not_array', false],
            [[], true],
            [[404], true],
            [[403], false],
            [[404, 403], true],
            [[403, 404], true],
            [[403, 401], false],
        ];
    }
}
