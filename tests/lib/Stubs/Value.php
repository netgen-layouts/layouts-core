<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Value as BaseValue;

final class Value extends BaseValue
{
    /**
     * @var int
     */
    public $status;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
