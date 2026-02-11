<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\LayoutName;
use Netgen\Layouts\Validator\LayoutNameValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(LayoutNameValidator::class)]
final class LayoutNameValidatorTest extends ValidatorTestCase
{
    private Stub&LayoutService $layoutServiceStub;

    protected function setUp(): void
    {
        $this->constraint = new LayoutName();

        parent::setUp();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(?string $value, bool $isValid): void
    {
        if ($value !== null && $value !== '') {
            $this->layoutServiceStub
                ->method('layoutNameExists')
                ->willReturn(!$isValid);
        }

        $this->assertValid($isValid, $value);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\LayoutName", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'My layout');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "int" given');

        $this->assertValid(true, 42);
    }

    /**
     * @return iterable<mixed>
     */
    public static function validateDataProvider(): iterable
    {
        return [
            ['My layout', true],
            ['My layout', false],
            [null, true],
            ['', true],
        ];
    }

    protected function getConstraintValidator(): ConstraintValidatorInterface
    {
        $this->layoutServiceStub = self::createStub(LayoutService::class);

        return new LayoutNameValidator($this->layoutServiceStub);
    }
}
