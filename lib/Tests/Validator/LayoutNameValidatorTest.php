<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\BlockManager\Validator\Constraint\LayoutName;
use Netgen\BlockManager\Validator\LayoutNameValidator;

class LayoutNameValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutServiceMock;

    public function setUp()
    {
        parent::setUp();

        $this->constraint = new LayoutName();
    }

    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public function getValidator()
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);

        return new LayoutNameValidator($this->layoutServiceMock);
    }

    /**
     * @param string $layoutName
     * @param bool $exists
     *
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::__construct
     * @covers \Netgen\BlockManager\Validator\LayoutNameValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($layoutName, $exists)
    {
        $this->layoutServiceMock
            ->expects($this->once())
            ->method('layoutNameExists')
            ->with($this->equalTo($layoutName))
            ->will($this->returnValue($exists));

        $this->assertValid(!$exists, $layoutName);
    }

    public function validateDataProvider()
    {
        return array(
            array('My layout', true),
            array('My layout', false),
        );
    }
}
