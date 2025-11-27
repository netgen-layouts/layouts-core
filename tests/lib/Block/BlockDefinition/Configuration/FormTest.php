<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block\BlockDefinition\Configuration;

use Netgen\Layouts\Block\BlockDefinition\Configuration\Form;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Form::class)]
final class FormTest extends TestCase
{
    private Form $form;

    protected function setUp(): void
    {
        $this->form = Form::fromArray(['identifier' => 'content', 'type' => 'form_type']);
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('content', $this->form->identifier);
    }

    public function testGetType(): void
    {
        self::assertSame('form_type', $this->form->type);
    }
}
