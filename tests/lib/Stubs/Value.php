<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Utils\HydratorTrait;

final class Value
{
    use HydratorTrait;

    public $a;

    protected $b;

    private $c;

    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function getC()
    {
        return $this->c;
    }
}
