<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType\PathInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(PathInfo::class)]
final class PathInfoTest extends TestCase
{
    private PathInfo $targetType;

    protected function setUp(): void
    {
        $this->targetType = new PathInfo();
    }

    public function testGetType(): void
    {
        self::assertSame('path_info', $this->targetType::getType());
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
