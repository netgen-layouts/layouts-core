<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\RoutePrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(RoutePrefix::class)]
final class RoutePrefixTest extends TestCase
{
    private RoutePrefix $targetType;

    protected function setUp(): void
    {
        $this->targetType = new RoutePrefix();
    }

    public function testGetType(): void
    {
        self::assertSame('route_prefix', $this->targetType::getType());
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

    /**
     * @return iterable<mixed>
     */
    public static function validationDataProvider(): iterable
    {
        return [
            ['route_name', true],
            ['', false],
            [null, false],
        ];
    }
}
