<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\Core\Values\ValueStatusTrait;
use Netgen\BlockManager\Value as BaseValue;

final class Value extends BaseValue implements APIValue
{
    use ValueStatusTrait;

    /**
     * @var mixed
     */
    public $someProperty;

    /**
     * @var mixed
     */
    public $someOtherProperty;
}
