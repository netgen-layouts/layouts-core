<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(Route::class)]
final class RouteTest extends TestCase
{
    private Route $targetType;

    protected function setUp(): void
    {
        $this->targetType = new Route();
    }

    public function testGetType(): void
    {
        self::assertSame('route', $this->targetType::getType());
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    public function testProvideValue(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'my_cool_route');

        self::assertSame(
            'my_cool_route',
            $this->targetType->provideValue($request),
        );
    }

    public static function validationDataProvider(): iterable
    {
        return [
            ['route_name', true],
            ['', false],
            [null, false],
        ];
    }
}
