<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\Core\Service\Validator\Validator;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\TestCase\ValidatorFactory;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

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
            ->setConstraintValidatorFactory(new ValidatorFactory())
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
            $this->expectException(InvalidArgumentException::class);
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
            $this->expectException(InvalidArgumentException::class);
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
            $this->expectException(InvalidArgumentException::class);
        }

        $this->validator->validatePosition($position, null, $isRequired);
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
}
