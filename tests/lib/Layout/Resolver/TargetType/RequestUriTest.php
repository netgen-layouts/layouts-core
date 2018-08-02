<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class RequestUriTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri
     */
    private $targetType;

    public function setUp(): void
    {
        $this->targetType = new RequestUri();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getType
     */
    public function testGetType(): void
    {
        $this->assertSame('request_uri', $this->targetType::getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\TargetType\RequestUri::provideValue
     */
    public function testProvideValue(): void
    {
        $request = Request::create('/the/answer', Request::METHOD_GET, ['a' => 42]);

        $this->assertSame(
            '/the/answer?a=42',
            $this->targetType->provideValue($request)
        );
    }

    public function validationProvider(): array
    {
        return [
            ['/some/route?id=42', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
