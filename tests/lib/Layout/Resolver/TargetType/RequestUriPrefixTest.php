<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\RequestUriPrefix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(RequestUriPrefix::class)]
final class RequestUriPrefixTest extends TestCase
{
    private RequestUriPrefix $targetType;

    protected function setUp(): void
    {
        $this->targetType = new RequestUriPrefix();
    }

    public function testGetType(): void
    {
        self::assertSame('request_uri_prefix', $this->targetType::getType());
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
        $request = Request::create('/the/answer', Request::METHOD_GET, ['a' => 42]);

        self::assertSame(
            '/the/answer?a=42',
            $this->targetType->provideValue($request),
        );
    }

    public static function validationDataProvider(): iterable
    {
        return [
            ['/some/route?id=42', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
