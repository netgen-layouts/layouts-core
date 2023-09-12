<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\ConditionType;

use Exception;
use Netgen\Layouts\Layout\Resolver\ConditionType\Exception as ExceptionConditionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\ErrorHandler\Exception\FlattenException as ErrorHandlerFlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validation;

use function class_exists;

final class ExceptionTest extends TestCase
{
    private ExceptionConditionType $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new ExceptionConditionType();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::getType
     */
    public function testGetType(): void
    {
        self::assertSame('exception', $this->conditionType::getType());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::getConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::matches
     *
     * @param mixed $value
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');

        $exception = new HttpException(404);
        $request->attributes->set('exception', $exception);

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::matches
     *
     * @param mixed $value
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatchesWithoutHttpException($value, bool $matches): void
    {
        $request = Request::create('/');

        $exception = new Exception();
        if (class_exists(ErrorHandlerFlattenException::class)) {
            $exception = ErrorHandlerFlattenException::createFromThrowable($exception, 404);
        } elseif (class_exists(DebugFlattenException::class)) {
            $exception = DebugFlattenException::create($exception, 404);
        }

        $request->attributes->set('exception', $exception);

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithNoException(): void
    {
        $request = Request::create('/');

        self::assertFalse($this->conditionType->matches($request, [404]));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\ConditionType\Exception::matches
     */
    public function testMatchesWithInvalidException(): void
    {
        $request = Request::create('/');

        $request->attributes->set('exception', new Exception());

        self::assertFalse($this->conditionType->matches($request, [404]));
    }

    public static function validationDataProvider(): iterable
    {
        return [
            [[200], false],
            [[399], false],
            [[400], true],
            [[401], true],
            [[404], true],
            [[404, 403], true],
            [[403, 700], false],
            [[403, 200], false],
            [[599], true],
            [[600], false],
            [[601], false],
            [[700], false],
            [[], true],
            [null, false],
        ];
    }

    public static function matchesDataProvider(): iterable
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
