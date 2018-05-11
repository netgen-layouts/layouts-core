<?php

namespace Netgen\BlockManager\Behat\Page\App;

use Netgen\BlockManager\Behat\Page\SymfonyPage;

final class IndexPage extends SymfonyPage
{
    public function getRouteName()
    {
        return 'ngbm_app';
    }

    public function verifyLayout($layoutName)
    {
        $this->hasElement('layout_name', ['%layout-name%' => $layoutName]);
    }

    protected function getDefinedElements()
    {
        return [
            'layout_name' => 'span.js-layout-name:contains("%layout-name%")',
        ];
    }
}
