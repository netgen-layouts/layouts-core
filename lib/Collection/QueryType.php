<?php

namespace Netgen\BlockManager\Collection;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Exception\Collection\QueryTypeException;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;
use Netgen\BlockManager\Value;

/**
 * @final
 */
class QueryType extends Value implements QueryTypeInterface
{
    use ParameterCollectionTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\Configuration\Form[]
     */
    protected $forms = array();

    /**
     * @var \Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface
     */
    protected $handler;

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getForms()
    {
        return $this->forms;
    }

    public function hasForm($formName)
    {
        return isset($this->forms[$formName]);
    }

    public function getForm($formName)
    {
        if (!$this->hasForm($formName)) {
            throw QueryTypeException::noForm($this->type, $formName);
        }

        return $this->forms[$formName];
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        return $this->handler->getValues($query, $offset, $limit);
    }

    public function getCount(Query $query)
    {
        return $this->handler->getCount($query);
    }

    public function isContextual(Query $query)
    {
        return $this->handler->isContextual($query);
    }
}
