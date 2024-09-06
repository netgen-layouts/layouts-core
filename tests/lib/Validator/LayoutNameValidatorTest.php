<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Validator\Constraint\LayoutName;
use Netgen\Layouts\Validator\LayoutNameValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class LayoutNameValidatorTest extends ValidatorTestCase
{
    private MockObject $layoutServiceMock;

    protected function setUp(): void
    {
        $this->constraint = new LayoutName();

        parent::setUp();
    }

    /**
     * @covers \Netgen\Layouts\Validator\LayoutNameValidator::__construct
     * @covers \Netgen\Layouts\Validator\LayoutNameValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $value, bool $isValid): void
    {
        if ($value !== null && $value !== '') {
            $this->layoutServiceMock
                ->expects(self::once())
                ->method('layoutNameExists')
                ->with(self::identicalTo($value))
                ->willReturn(!$isValid);
        }

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\Layouts\Validator\LayoutNameValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Validator\Constraint\LayoutName", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'My layout');
    }

    /**
     * @covers \Netgen\Layouts\Validator\LayoutNameValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['My layout', true],
            ['My layout', false],
            [null, true],
            ['', true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return new LayoutNameValidator($this->layoutServiceMock);
    }
}
