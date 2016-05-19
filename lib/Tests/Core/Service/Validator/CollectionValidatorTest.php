<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeWithRequiredParameter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType as QueryTypeStub;
use Netgen\BlockManager\Validator\Constraint\ValueType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CollectionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $validatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeRegistryMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $collectionValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->validatorMock = $this->getMock(ValidatorInterface::class);
        $this->queryTypeRegistryMock = $this->getMock(QueryTypeRegistryInterface::class);

        $this->collectionValidator = new CollectionValidator($this->queryTypeRegistryMock);
        $this->collectionValidator->setValidator($this->validatorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     */
    public function testValidateCollectionCreateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('My collection'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $collectionCreateStruct = new CollectionCreateStruct(
            array(
                'type' => Collection::TYPE_NAMED,
                'name' => 'My collection',
            )
        );

        $this->collectionValidator->validateCollectionCreateStruct($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionUpdateStruct
     */
    public function testValidateCollectionUpdateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('Updated collection'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $collectionUpdateStruct = new CollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Updated collection';

        $this->collectionValidator->validateCollectionUpdateStruct($collectionUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     */
    public function testValidateItemCreateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo(Item::TYPE_OVERRIDE),
                array(
                    new Constraints\Choice(
                        array(
                            'choices' => array(
                                Item::TYPE_MANUAL,
                                Item::TYPE_OVERRIDE,
                            ),
                            'strict' => true,
                        )
                    ),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo('42'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'scalar')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(2))
            ->method('validate')
            ->with(
                $this->equalTo('value'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                    new ValueType(),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $itemCreateStruct = new ItemCreateStruct();
        $itemCreateStruct->type = Item::TYPE_OVERRIDE;
        $itemCreateStruct->valueId = '42';
        $itemCreateStruct->valueType = 'value';

        $this->collectionValidator->validateItemCreateStruct($itemCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     */
    public function testValidateQueryCreateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('my_query'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo('query_type'),
                array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(2))
            ->method('validate')
            ->with(
                $this->equalTo(array('param' => 'value')),
                array(
                    new Constraints\Collection(
                        array(
                            'fields' => array(
                                'param' => new Constraints\Required(array(new Constraints\NotBlank())),
                            ),
                        )
                    ),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->with($this->equalTo('query_type'))
            ->will($this->returnValue(new QueryTypeWithRequiredParameter()));

        $queryCreateStruct = new QueryCreateStruct();
        $queryCreateStruct->identifier = 'my_query';
        $queryCreateStruct->type = 'query_type';
        $queryCreateStruct->setParameters(array('param' => 'value'));

        $this->collectionValidator->validateQueryCreateStruct($queryCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @covers \Netgen\BlockManager\Core\Service\Validator\Validator::buildParameterValidationFields
     */
    public function testValidateQueryUpdateStruct()
    {
        $this->validatorMock
            ->expects($this->at(0))
            ->method('validate')
            ->with(
                $this->equalTo('updated_query'),
                array(
                    new Constraints\Type(array('type' => 'string')),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->validatorMock
            ->expects($this->at(1))
            ->method('validate')
            ->with(
                $this->equalTo(array('param' => 'value')),
                array(
                    new Constraints\Collection(
                        array(
                            'fields' => array(
                                'param' => new Constraints\Optional(array()),
                            ),
                        )
                    ),
                )
            )
            ->will($this->returnValue(new ConstraintViolationList()));

        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->with($this->equalTo('query_type'))
            ->will($this->returnValue(new QueryTypeStub()));

        $queryUpdateStruct = new QueryUpdateStruct();
        $queryUpdateStruct->identifier = 'updated_query';
        $queryUpdateStruct->setParameters(array('param' => 'value'));

        $query = new Query(
            array(
                'type' => 'query_type',
            )
        );

        $this->collectionValidator->validateQueryUpdateStruct($query, $queryUpdateStruct);
    }
}
