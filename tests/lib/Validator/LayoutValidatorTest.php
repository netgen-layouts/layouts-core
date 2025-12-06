<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\Layout;
use Netgen\Layouts\Validator\LayoutValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(LayoutValidator::class)]
final class LayoutValidatorTest extends ValidatorTestCase
{
    private Stub&LayoutService $layoutServiceStub;

    protected function setUp(): void
    {
        $this->constraint = new Layout();

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(mixed $value, bool $isValid): void
    {
        if ($value !== null) {
            $this->layoutServiceStub
                ->method('layoutExists')
                ->with(self::equalTo(Uuid::fromString($value)))
                ->willReturn($isValid);
        }

        $this->assertValid($isValid, $value);
    }

    public function testValidateWithNonExistentValue(): void
    {
        $this->constraint = new Layout(allowInvalid: true);

        $this->assertValid(true, 'ffffffff-ffff-ffff-ffff-ffffffffffff');
    }

    public function testValidateWithInvalidUuidFormat(): void
    {
        $this->assertValid(false, 'abc');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\Layout", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 42);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['81168ed3-86f9-55ea-b153-101f96f2c136', true],
            ['71cbe281-430c-51d5-8e21-c3cc4e656dac', false],
            [null, true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        return new LayoutValidator($this->layoutServiceStub);
    }
}
