<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\PathInfoPrefix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class PathInfoPrefixTest extends TestCase
{
    private PathInfoPrefix $targetType;

    protected function setUp(): void
    {
        $this->targetType = new PathInfoPrefix();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\PathInfoPrefix::getType
     */
    public function testGetType(): void
    {
        self::assertSame('path_info_prefix', $this->targetType::getType());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\PathInfoPrefix::getConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\TargetType\PathInfoPrefix::provideValue
     */
    public function testProvideValue(): void
    {
        $request = Request::create('/the/answer');

        self::assertSame(
            '/the/answer',
            $this->targetType->provideValue($request),
        );
    }

    public static function validationDataProvider(): iterable
    {
        return [
            ['/some/route', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
