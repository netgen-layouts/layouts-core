<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Core\Service\Validator\Validator;
use Netgen\BlockManager\Exception\ValidationFailedException;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ValidatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $baseValidator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\Validator
     */
    protected $validator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->baseValidator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this))
            ->getValidator();

        $this->validator = $this->getMockForAbstractClass(Validator::class);
        $this->validator->setValidator($this->baseValidator);
    }

    /**
     * @param int|string $id
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateId
     * @dataProvider validateIdDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateId($id, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->validator->validateId($id);
    }

    /**
     * @param string $identifier
     * @param bool $isRequired
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateIdentifier
     * @dataProvider validateIdentifierDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateIdentifier($identifier, $isRequired, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->validator->validateIdentifier($identifier, null, $isRequired);
    }

    /**
     * @param int $position
     * @param bool $isRequired
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validatePosition
     * @dataProvider validatePositionDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidatePosition($position, $isRequired, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->validator->validatePosition($position, null, $isRequired);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::validateOffsetAndLimit
     * @dataProvider validateOffsetAndLimitDataProvider
     * @doesNotPerformAssertions
     */
    public function testValidateOffsetAndLimit($offset, $limit, $isValid)
    {
        if (!$isValid) {
            $this->expectException(ValidationFailedException::class);
        }

        $this->validator->validateOffsetAndLimit($offset, $limit);
    }

    public function validateIdDataProvider()
    {
        return array(
            array(24, true),
            array('24', true),
            array('', false),
            array(array(), false),
            array(null, false),
        );
    }

    public function validateIdentifierDataProvider()
    {
        return array(
            array(24, false, false),
            array(24, true, false),
            array(null, false, true),
            array(null, true, false),
            array('identifier', false, true),
            array('identifier', true, true),
            array('identifier_1', false, true),
            array('identifier_2', true, true),
            array('123identifier', false, false),
            array('345identifier', true, false),
            array('an identifier', false, false),
            array('other identifier', true, false),
            array('', false, false),
            array('', true, false),
        );
    }

    public function validatePositionDataProvider()
    {
        return array(
            array(-5, false, false),
            array(-5, true, false),
            array(0, false, true),
            array(0, true, true),
            array(24, false, true),
            array(24, true, true),
            array(null, false, true),
            array(null, true, false),
            array('identifier', false, false),
            array('identifier', true, false),
        );
    }

    public function validateOffsetAndLimitDataProvider()
    {
        return array(
            array(0, null, true),
            array(5, null, true),
            array('5', null, false),
            array(null, null, false),
            array(0, 1, true),
            array(5, 1, true),
            array('5', 1, false),
            array(null, 1, false),
            array(5, '5', false),
        );
    }
}