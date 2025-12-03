<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\View\View;

final class ParameterView extends View implements ParameterViewInterface
{
    public string $identifier {
        get => 'parameter';
    }

    public function __construct(
        public private(set) Parameter $parameterValue,
    ) {
        $this->addInternalParameter('parameter', $this->parameterValue);
    }
}
