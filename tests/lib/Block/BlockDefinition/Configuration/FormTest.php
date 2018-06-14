<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Form;
use PHPUnit\Framework\TestCase;

final class FormTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form
     */
    private $form;

    public function setUp(): void
    {
        $this->form = new Form(['identifier' => 'content', 'type' => 'form_type']);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('content', $this->form->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\Form::getType
     */
    public function testGetType(): void
    {
        $this->assertEquals('form_type', $this->form->getType());
    }
}
