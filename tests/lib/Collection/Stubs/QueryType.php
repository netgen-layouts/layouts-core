<?php

namespace Netgen\BlockManager\Tests\Collection\Stubs;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Exception\Parameters\ParameterException;

final class QueryType implements QueryTypeInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var \Netgen\BlockManager\Tests\Collection\Stubs\QueryTypeHandler
     */
    private $handler;

    /**
     * @var array
     */
    private $values;

    /**
     * Constructor.
     *
     * @param string $type
     * @param array $values
     * @param int $count
     * @param bool $contextual
     */
    public function __construct($type, array $values = array(), $count = null, $contextual = false)
    {
        $this->type = $type;
        $this->values = $values;

        $this->handler = new QueryTypeHandler($this->values, $count, $contextual);
    }

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->handler->getParameters();
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if ($this->hasParameter($parameterName)) {
            return $this->handler->getParameters()[$parameterName];
        }

        throw new ParameterException('parameterName', 'Parameter is missing.');
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->handler->getParameters()[$parameterName]);
    }

    /**
     * Returns the values from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    /**
     * Returns the value count from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query)
    {
        return $this->handler->getCount($query);
    }

    /**
     * Returns if the provided query is dependent on a context, i.e. current request.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return bool
     */
    public function isContextual(Query $query)
    {
        return $this->handler->isContextual($query);
    }

    /**
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the query type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->type;
    }

    /**
     * Returns all forms.
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form[]
     */
    public function getForms()
    {
        return array();
    }

    /**
     * Returns if the query type has a form with provided name.
     *
     * @param $formName
     *
     * @return bool
     */
    public function hasForm($formName)
    {
        return false;
    }

    /**
     * Returns the form for provided form name.
     *
     * @param $formName
     *
     * @throws \Netgen\BlockManager\Exception\Collection\QueryTypeException If query type does not have the form
     *
     * @return \Netgen\BlockManager\Collection\QueryType\Configuration\Form
     */
    public function getForm($formName)
    {
    }

    /**
     * @return \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
