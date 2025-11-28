<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Stubs;

use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\View as BaseView;

final class View extends BaseView
{
    public string $identifier {
        get => 'stub';
    }

    public function __construct(Value $value)
    {
        $this->addInternalParameter('value', $value);
    }
}
