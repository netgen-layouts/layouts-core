<?php

namespace Netgen\BlockManager\Tests\Validator;

use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Validator\QueryTypeValidator;
use Netgen\BlockManager\Validator\Constraint\QueryType;

class QueryTypeValidatorTest extends ValidatorTest
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeRegistryMock;

    /**
     * @var \Netgen\BlockManager\Validator\QueryTypeValidator
     */
    protected $validator;

    public function setUp()
    {
        parent::setUp();

        $this->queryTypeRegistryMock = $this->getMock(
            QueryTypeRegistryInterface::class
        );

        $this->validator = new QueryTypeValidator($this->queryTypeRegistryMock);
        $this->validator->initialize($this->executionContextMock);
    }

    /**
     * @covers \Netgen\BlockManager\Validator\QueryTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\QueryTypeValidator::validate
     */
    public function testValidate()
    {
        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('hasQueryType')
            ->with($this->equalTo('query_type'))
            ->will($this->returnValue(true));

        $this->executionContextMock
            ->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('query_type', new QueryType());
    }

    /**
     * @covers \Netgen\BlockManager\Validator\QueryTypeValidator::__construct
     * @covers \Netgen\BlockManager\Validator\QueryTypeValidator::validate
     */
    public function testValidateFailed()
    {
        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('hasQueryType')
            ->with($this->equalTo('query_type'))
            ->will($this->returnValue(false));

        $this->executionContextMock
            ->expects($this->once())
            ->method('buildViolation')
            ->will($this->returnValue($this->violationBuilderMock));

        $this->validator->validate('query_type', new QueryType());
    }
}
