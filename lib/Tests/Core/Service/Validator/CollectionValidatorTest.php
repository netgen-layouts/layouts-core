<?php

namespace Netgen\BlockManager\Tests\Core\Service\Validator;

use Netgen\BlockManager\API\Values\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\ItemCreateStruct;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Collection\Registry\QueryTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\API\Values\CollectionUpdateStruct;
use Netgen\BlockManager\Core\Values\Collection\Collection;
use Netgen\BlockManager\Core\Values\Collection\Item;
use Netgen\BlockManager\Core\Values\Collection\Query;
use Netgen\BlockManager\Core\Values\QueryCreateStruct;
use Netgen\BlockManager\Core\Values\QueryUpdateStruct;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeWithRequiredParameter;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType as QueryTypeStub;
use Netgen\BlockManager\Tests\Validator\ValidatorFactory;
use Symfony\Component\Validator\Validation;

class CollectionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeHandlerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queryTypeRegistryMock;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Core\Service\Validator\CollectionValidator
     */
    protected $collectionValidator;

    /**
     * Sets up the test.
     */
    public function setUp()
    {
        $this->queryTypeRegistryMock = $this->createMock(QueryTypeRegistryInterface::class);

        $this->queryTypeHandlerMock = $this->createMock(QueryTypeHandlerInterface::class);
        $this->queryTypeConfigMock = $this->createMock(Configuration::class);

        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $this->collectionValidator = new CollectionValidator($this->queryTypeRegistryMock);
        $this->collectionValidator->setValidator($this->validator);
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::__construct
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionCreateStruct
     * @dataProvider validateCollectionCreateStructProvider
     */
    public function testValidateCollectionCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->collectionValidator->validateCollectionCreateStruct(new CollectionCreateStruct($params))
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateCollectionUpdateStruct
     * @dataProvider validateCollectionUpdateStructProvider
     */
    public function testValidateCollectionUpdateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->collectionValidator->validateCollectionUpdateStruct(new CollectionUpdateStruct($params))
        );
    }

    /**
     * @param array $params
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateItemCreateStruct
     * @dataProvider validateItemCreateStructProvider
     */
    public function testValidateItemCreateStruct(array $params, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->collectionValidator->validateItemCreateStruct(new ItemCreateStruct($params))
        );
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryCreateStruct
     * @dataProvider validateQueryCreateStructProvider
     */
    public function testValidateQueryCreateStruct(array $params, $isValid)
    {
        $queryType = new QueryTypeWithRequiredParameter(
            'query_type',
            $this->queryTypeHandlerMock,
            $this->queryTypeConfigMock
        );

        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->will($this->returnValue($queryType));

        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->collectionValidator->validateQueryCreateStruct(new QueryCreateStruct($params))
        );
    }

    /**
     * @param array $params
     * @param array $isValid
     *
     * @covers \Netgen\BlockManager\Core\Service\Validator\CollectionValidator::validateQueryUpdateStruct
     * @dataProvider validateQueryUpdateStructProvider
     */
    public function testValidateQueryUpdateStruct(array $params, $isValid)
    {
        $queryType = new QueryTypeStub(
            'query_type',
            $this->queryTypeHandlerMock,
            $this->queryTypeConfigMock
        );

        $this->queryTypeRegistryMock
            ->expects($this->any())
            ->method('getQueryType')
            ->will($this->returnValue($queryType));

        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        self::assertTrue(
            $this->collectionValidator->validateQueryUpdateStruct(
                new Query(array('type' => 'query_type')),
                new QueryUpdateStruct($params)
            )
        );
    }

    public function validateCollectionCreateStructProvider()
    {
        return array(
            array(array('name' => null, 'type' => Collection::TYPE_MANUAL), true),
            array(array('name' => 'Collection', 'type' => Collection::TYPE_NAMED), true),
            array(array('name' => 23, 'type' => Collection::TYPE_NAMED), false),
            array(array('name' => null, 'type' => Collection::TYPE_NAMED), false),
            array(array('name' => '', 'type' => Collection::TYPE_NAMED), false),
            array(array('name' => null, 'type' => 23), false),
            array(array('name' => null, 'type' => null), false),
            array(array('name' => null, 'type' => 'type'), false),
        );
    }

    public function validateCollectionUpdateStructProvider()
    {
        return array(
            array(array('name' => 'Collection'), true),
            array(array('name' => 23), false),
            array(array('name' => null), false),
            array(array('name' => ''), false),
        );
    }

    public function validateItemCreateStructProvider()
    {
        return array(
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), true),
            array(array('valueId' => '42', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), true),
            array(array('valueId' => null, 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => '', 'valueType' => 'value', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => 'nonexistent', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => '', 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => null, 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => 42, 'type' => Item::TYPE_MANUAL), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => 23), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => 'type'), false),
            array(array('valueId' => 42, 'valueType' => 'value', 'type' => null), false),
        );
    }

    public function validateQueryCreateStructProvider()
    {
        return array(
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => null,
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => '',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 42,
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => null,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => '',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 42,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => '',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(
                        'param' => null,
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'type' => 'query_type',
                    'parameters' => array(),
                ),
                false,
            ),
        );
    }

    public function validateQueryUpdateStructProvider()
    {
        return array(
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => null,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => '',
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 42,
                    'parameters' => array(
                        'param' => 'value',
                    ),
                ),
                false,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => '',
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(
                        'param' => null,
                    ),
                ),
                true,
            ),
            array(
                array(
                    'identifier' => 'my_query',
                    'parameters' => array(),
                ),
                true,
            ),
        );
    }
}
