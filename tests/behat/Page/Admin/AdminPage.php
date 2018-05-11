<?php

namespace Netgen\BlockManager\Behat\Page\Admin;

use Netgen\BlockManager\Behat\Exception\PageException;
use Netgen\BlockManager\Behat\Page\SymfonyPage;

abstract class AdminPage extends SymfonyPage
{
    public function openModal(callable $opener)
    {
        $opener();

        $this->waitForElement(10, 'modal_dialog');
        $this->verifyModalOpen();
    }

    public function submitModal()
    {
        $this->getElement('modal_confirm_button')->press();
        $this->waitForElement(10, 'modal_dialog', [], true);
        $this->verifyModalClosed();
    }

    public function submitModalWithError()
    {
        $this->getElement('modal_confirm_button')->press();
        $this->waitForElement(10, 'modal_errors');
        $this->verifyModalOpen();
    }

    public function cancelModal()
    {
        $this->getElement('modal_cancel_button')->press();
        $this->waitForElement(10, 'modal_dialog', [], true);
        $this->verifyModalClosed();
    }

    public function verifyModalOpen()
    {
        if ($this->hasElement('modal_dialog')) {
            return;
        }

        throw new PageException('Modal dialog was expected to be open');
    }

    public function verifyModalClosed()
    {
        if (!$this->hasElement('modal_dialog')) {
            return;
        }

        throw new PageException('Modal dialog was expected to be closed');
    }

    public function modalErrorExists($errorMessage)
    {
        return $this->hasElement('modal_error', ['%error-message%' => $errorMessage]);
    }

    protected function getDefinedElements()
    {
        return [
            'modal_dialog' => '.nl-modal',
            'modal_cancel_button' => '.nl-modal button.action-cancel',
            'modal_confirm_button' => '.nl-modal button.action-apply',

            'modal_errors' => '.nl-modal ul.errors',
            'modal_error' => '.nl-modal ul.errors li:contains("%error-message%")',
        ];
    }
}
