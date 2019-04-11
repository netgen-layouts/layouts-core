<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Validator;

use Exception;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Tests\Validator\Stubs\ValueValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorTraitTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $validatorMock;

    /**
     * @var \Netgen\Layouts\Tests\Validator\Stubs\ValueValidator
     */
    private $validator;

    public function setUp(): void
    {
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validator = new ValueValidator();
        $this->validator->setValidator($this->validatorMock);
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValidatorTrait::setValidator
     * @covers \Netgen\Layouts\Validator\ValidatorTrait::validate
     */
    public function testValidate(): void
    {
        $constraints = [new Constraints\NotBlank()];
        $this->validatorMock
            ->expects(self::once())
            ->method('validate')
            ->with(
                self::identicalTo('some value'),
                self::identicalTo($constraints)
            )
            ->willReturn(new ConstraintViolationList());

        $this->validator->validateValue('some value', $constraints);
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValidatorTrait::validate
     */
    public function testValidateThrowsValidationException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('There was an error validating "value": Value should not be blank');

        $constraints = [new Constraints\NotBlank()];
        $this->validatorMock
            ->expects(self::once())
            ->method('validate')
            ->with(
                self::identicalTo('some value'),
                self::identicalTo($constraints)
            )->willReturn(
                new ConstraintViolationList(
                    [
                        $this->createConfiguredMock(
                            ConstraintViolationInterface::class,
                            ['getMessage' => 'Value should not be blank']
                        ),
                    ]
                )
            );

        $this->validator->validateValue('some value', $constraints, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Validator\ValidatorTrait::validate
     */
    public function testValidateThrowsValidationExceptionOnOtherException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Test exception text');

        $this->validatorMock
            ->expects(self::once())
            ->method('validate')
            ->willThrowException(new Exception('Test exception text'));

        $this->validator->validateValue('some value', [new Constraints\NotBlank()]);
    }
}
