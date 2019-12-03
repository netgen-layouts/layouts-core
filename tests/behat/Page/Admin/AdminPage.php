<?php

declare(strict_types=1);

namespace Netgen\Layouts\Behat\Page\Admin;

use Netgen\Layouts\Behat\Exception\PageException;
use Netgen\Layouts\Behat\Page\SymfonyPage;

abstract class AdminPage extends SymfonyPage
{
    public function openModal(callable $opener): void
    {
        $opener();

        $this->waitForElement(10, 'modal_dialog');
        $this->verifyModalOpen();
    }

    public function submitModal(): void
    {
        $this->getElement('modal_confirm_button')->press();
    }

    public function cancelModal(): void
    {
        $this->getElement('modal_cancel_button')->press();
    }

    public function verifyModalOpen(): void
    {
        if ($this->hasElement('modal_dialog')) {
            return;
        }

        throw new PageException('Modal dialog was expected to be open');
    }

    public function verifyModalClosed(): void
    {
        if (!$this->hasElement('modal_dialog')) {
            return;
        }

        throw new PageException('Modal dialog was expected to be closed');
    }

    public function verifyModalErrorDoesNotExist(): void
    {
        $this->waitForElement(10, 'modal_dialog', [], true);
        $this->verifyModalClosed();
    }

    public function modalErrorExists(string $errorMessage): bool
    {
        $this->waitForElement(10, 'modal_errors');
        $this->verifyModalOpen();

        return $this->hasElement('modal_error', ['%error-message%' => $errorMessage]);
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
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
