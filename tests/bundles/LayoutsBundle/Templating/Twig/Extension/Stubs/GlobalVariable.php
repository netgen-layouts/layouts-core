<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Extension\Stubs;

use Netgen\Layouts\API\Values\Layout\Layout;

final class GlobalVariable
{
    private Layout $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }
}
