<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Exception\View\ViewException;
use Symfony\Component\HttpFoundation\Response;

use function array_key_exists;
use function get_debug_type;

abstract class View implements ViewInterface
{
    /**
     * @var array<string, mixed>
     */
    protected array $parameters = [];

    private ?string $context = null;

    private ?string $fallbackContext = null;

    private ?string $template = null;

    private ?Response $response = null;

    /**
     * @var array<string, mixed>
     */
    private array $customParameters = [];

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    public function getFallbackContext(): ?string
    {
        return $this->fallbackContext;
    }

    public function setFallbackContext(string $fallbackContext): void
    {
        $this->fallbackContext = $fallbackContext;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function hasParameter(string $identifier): bool
    {
        return array_key_exists($identifier, $this->parameters)
            || array_key_exists($identifier, $this->customParameters);
    }

    public function getParameter(string $identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw ViewException::parameterNotFound($identifier, get_debug_type($this));
        }

        if (array_key_exists($identifier, $this->parameters)) {
            return $this->parameters[$identifier];
        }

        return $this->customParameters[$identifier];
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters + $this->customParameters;
    }

    public function addParameter(string $parameterName, $parameterValue): void
    {
        $this->customParameters[$parameterName] = $parameterValue;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function addParameters(array $parameters): void
    {
        $this->customParameters = $parameters + $this->customParameters;
    }
}
