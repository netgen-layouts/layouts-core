<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\RequestUri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(RequestUri::class)]
final class RequestUriTest extends TestCase
{
    private RequestUri $targetType;

    protected function setUp(): void
    {
        $this->targetType = new RequestUri();
    }

    public function testGetType(): void
    {
        self::assertSame('request_uri', $this->targetType::getType());
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

    /**
     * @return iterable<mixed>
     */
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
