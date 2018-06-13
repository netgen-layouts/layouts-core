<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Behat\Page\Admin\SharedLayouts;

use Netgen\BlockManager\Behat\Page\Admin\AdminPage;

final class IndexPage extends AdminPage
{
    public function getRouteName()
    {
        return 'ngbm_admin_shared_layouts_index';
    }

    public function createLayout()
    {
        $this->getElement('create_new_layout')->click();
    }

    public function editLayout($layoutName)
    {
        $this->getElement('actions_dropdown', ['%layout-name%' => $layoutName])->press();
        $this->getElement('edit_layout_action', ['%layout-name%' => $layoutName])->click();
    }

    public function clickLayoutName($layoutName)
    {
        $this->getElement('layout_name', ['%layout-name%' => $layoutName])->click();
    }

    public function openDuplicateLayoutModal($layoutName)
    {
        $this->openModal(
            function () use ($layoutName) {
                $this->getElement('actions_dropdown', ['%layout-name%' => $layoutName])->press();
                $this->getElement('copy_layout_action', ['%layout-name%' => $layoutName])->press();
            }
        );
    }

    public function openDeleteLayoutModal($layoutName)
    {
        $this->openModal(
            function () use ($layoutName) {
                $this->getElement('actions_dropdown', ['%layout-name%' => $layoutName])->press();
                $this->getElement('delete_layout_action', ['%layout-name%' => $layoutName])->press();
            }
        );
    }

    public function layoutExists($layoutName)
    {
        return $this->hasElement('layout', ['%layout-name%' => $layoutName]);
    }

    public function nameDuplicatedLayout($layoutName)
    {
        $this->getDocument()->fillField('copy_name', $layoutName);
    }

    protected function getDefinedElements()
    {
        return array_merge(
            parent::getDefinedElements(),
            [
                'layout' => '.nl-layout [data-name="%layout-name%"]',
                'layout_name' => '.nl-layout-name a:contains("%layout-name%")',

                'create_new_layout' => 'a#add-new-button',

                'actions_dropdown' => '.nl-layout [data-name="%layout-name%"] button.nl-dropdown-toggle',
                'edit_layout_action' => '.nl-layout [data-name="%layout-name%"] a.js-layout-edit',
                'copy_layout_action' => '.nl-layout [data-name="%layout-name%"] button.js-layout-copy',
                'delete_layout_action' => '.nl-layout [data-name="%layout-name%"] button.js-layout-delete',
            ]
        );
    }
}
