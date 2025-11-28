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
    final protected array $parameters = [];

    private ?string $context = null;

    private ?string $fallbackContext = null;

    private ?string $template = null;

    private ?Response $response = null;

    /**
     * @var array<string, mixed>
     */
    private array $customParameters = [];

    final public function getContext(): ?string
    {
        return $this->context;
    }

    final public function setContext(string $context): void
    {
        $this->context = $context;
    }

    final public function getFallbackContext(): ?string
    {
        return $this->fallbackContext;
    }

    final public function setFallbackContext(string $fallbackContext): void
    {
        $this->fallbackContext = $fallbackContext;
    }

    final public function getTemplate(): ?string
    {
        return $this->template;
    }

    final public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    final public function getResponse(): ?Response
    {
        return $this->response;
    }

    final public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    final public function hasParameter(string $identifier): bool
    {
        return array_key_exists($identifier, $this->parameters)
            || array_key_exists($identifier, $this->customParameters);
    }

    final public function getParameter(string $identifier): mixed
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
    final public function getParameters(): array
    {
        return [...$this->customParameters, ...$this->parameters];
    }

    final public function addParameter(string $parameterName, mixed $parameterValue): void
    {
        $this->customParameters[$parameterName] = $parameterValue;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    final public function addParameters(array $parameters): void
    {
        $this->customParameters = [...$this->customParameters, ...$parameters];
    }
}
