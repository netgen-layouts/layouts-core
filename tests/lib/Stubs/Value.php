<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Netgen\Layouts\Utils\HydratorTrait;

final class Value
{
    use HydratorTrait;

    /**
     * @var mixed
     */
    public $a;

    /**
     * @var mixed
     */
    protected $b;

    /**
     * @var mixed
     */
    private $c;

    /**
     * @param mixed $a
     * @param mixed $b
     * @param mixed $c
     */
    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return mixed
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return mixed
     */
    public function getC()
    {
        return $this->c;
    }
}
