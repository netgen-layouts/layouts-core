<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    private Form $form;

    protected function setUp(): void
    {
        $this->form = Form::fromArray(['identifier' => 'content', 'type' => 'form_type']);
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('content', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Block\BlockDefinition\Configuration\Form::getType
     */
    public function testGetType(): void
    {
        self::assertSame('form_type', $this->form->getType());
    }
}
