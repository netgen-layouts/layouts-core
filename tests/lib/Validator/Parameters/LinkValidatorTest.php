<?php

namespace Netgen\BlockManager\Tests\Validator\Parameters;

use Netgen\BlockManager\Parameters\Value\LinkValue;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\Parameters\Link;
use Netgen\BlockManager\Validator\Parameters\LinkValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class LinkValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        $this->constraint = new Link();

        parent::setUp();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidator
     */
    public function getValidator()
    {
        return new LinkValidator();
    }

    /**
     * @param string $value
     * @param bool $required
     * @param array $valueTypes
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($value, $required, $valueTypes, $isValid)
    {
        $this->constraint->required = $required;
        $this->constraint->valueTypes = $valueTypes;

        $this->assertValid($isValid, $value);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Validator\Constraint\Parameters\Link", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, new LinkValue());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\Parameters\LinkValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Parameters\Value\LinkValue", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array(null, true, null, true),
            array(null, false, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 'suffix')), true, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'linkSuffix' => 42)), true, null, false),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => true)), true, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com', 'newWindow' => false)), true, null, true),
            array(new LinkValue(array('linkType' => null, 'link' => null)), true, null, true),
            array(new LinkValue(array('linkType' => null, 'link' => 'http://a.com')), true, null, false),
            array(new LinkValue(array('linkType' => null, 'link' => null)), false, null, true),
            array(new LinkValue(array('linkType' => null, 'link' => 'http://a.com')), false, null, false),
            array(new LinkValue(array('linkType' => 'url', 'link' => null)), true, null, false),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com')), true, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => null)), false, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'http://a.com')), false, null, true),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'invalid')), true, null, false),
            array(new LinkValue(array('linkType' => 'url', 'link' => 'invalid')), false, null, false),
            array(new LinkValue(array('linkType' => 'email', 'link' => null)), true, null, false),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'a@a.com')), true, null, true),
            array(new LinkValue(array('linkType' => 'email', 'link' => null)), false, null, true),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'a@a.com')), false, null, true),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'invalid')), true, null, false),
            array(new LinkValue(array('linkType' => 'email', 'link' => 'invalid')), false, null, false),
            array(new LinkValue(array('linkType' => 'phone', 'link' => null)), true, null, false),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 'a@a.com')), true, null, true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => null)), false, null, true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 'a@a.com')), false, null, true),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 42)), true, null, false),
            array(new LinkValue(array('linkType' => 'phone', 'link' => 42)), false, null, false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, null, false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, null, true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, null, true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, null, true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, null, false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, null, false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, array('value'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, array('value'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, array('value'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, array('value'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), true, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), true, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => null)), false, array('other'), true),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value://42')), false, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), true, array('other'), false),
            array(new LinkValue(array('linkType' => 'internal', 'link' => 'value')), false, array('other'), false),
        );
    }
}
