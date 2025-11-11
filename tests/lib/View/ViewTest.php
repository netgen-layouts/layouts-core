<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View;

use Netgen\Layouts\Exception\View\ViewException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View as ViewStub;
use Netgen\Layouts\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(View::class)]
final class ViewTest extends TestCase
{
    private ViewStub $view;

    private Value $value;

    protected function setUp(): void
    {
        $this->value = new Value();

        $this->view = new ViewStub($this->value);
    }

    public function testSetContext(): void
    {
        $this->view->setContext('context');

        self::assertSame('context', $this->view->getContext());
    }

    public function testSetTemplate(): void
    {
        $this->view->setTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->view->getTemplate());
    }

    public function testGetFallbackContext(): void
    {
        self::assertNull($this->view->getFallbackContext());
    }

    public function testSetFallbackContext(): void
    {
        $this->view->setFallbackContext('fallback');

        self::assertSame('fallback', $this->view->getFallbackContext());
    }

    public function testGetDefaultResponse(): void
    {
        self::assertNull($this->view->getResponse());
    }

    public function testSetResponse(): void
    {
        $response = new Response('response');

        $this->view->setResponse($response);

        self::assertSame($response, $this->view->getResponse());
    }

    public function testHasParameter(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertTrue($this->view->hasParameter('param'));
    }

    public function testHasParameterWithNoParam(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertFalse($this->view->hasParameter('other_param'));
    }

    public function testGetParameter(): void
    {
        $this->view->addParameter('param', 'value');

        self::assertSame('value', $this->view->getParameter('param'));
    }

    public function testGetParameterWithBuiltInParameter(): void
    {
        $this->view->addParameter('value', 'custom');

        self::assertSame($this->value, $this->view->getParameter('value'));
    }

    public function testGetParameterThrowsViewException(): void
    {
        $this->expectException(ViewException::class);
        $this->expectExceptionMessage('Parameter with "other_param" name was not found in "Netgen\Layouts\Tests\View\Stubs\View" view.');

        $this->view->addParameter('param', 'value');

        $this->view->getParameter('other_param');
    }

    public function testAddParameters(): void
    {
        $this->view->addParameters(
            [
                'some_param' => 'some_value',
                'some_other_param' => 'some_other_value',
            ],
        );

        $this->view->addParameters(
            [
                'some_param' => 'new_value',
                'third_param' => 'third_value',
            ],
        );

        self::assertSame(
            [
                'some_param' => 'new_value',
                'some_other_param' => 'some_other_value',
                'third_param' => 'third_value',
                'value' => $this->value,
            ],
            $this->view->getParameters(),
        );
    }
}
