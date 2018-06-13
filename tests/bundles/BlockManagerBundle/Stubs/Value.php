<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;
use Netgen\BlockManager\Core\Values\Value as BaseValue;

final class Value extends BaseValue implements APIValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $locale;
}
