<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Exception\View\ViewException;
use Symfony\Component\HttpFoundation\Response;

abstract class View implements ViewInterface
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    private $context;

    /**
     * @var string
     */
    private $fallbackContext;

    /**
     * @var string|null
     */
    private $template;

    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * @var array
     */
    private $customParameters = [];

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
        return array_key_exists($identifier, $this->parameters) ||
            array_key_exists($identifier, $this->customParameters);
    }

    public function getParameter(string $identifier)
    {
        if (!$this->hasParameter($identifier)) {
            throw ViewException::parameterNotFound($identifier, get_class($this));
        }

        if (array_key_exists($identifier, $this->parameters)) {
            return $this->parameters[$identifier];
        }

        return $this->customParameters[$identifier];
    }

    public function getParameters(): array
    {
        return $this->parameters + $this->customParameters;
    }

    public function addParameter(string $parameterName, $parameterValue): void
    {
        $this->customParameters[$parameterName] = $parameterValue;
    }

    public function addParameters(array $parameters): void
    {
        $this->customParameters = $parameters + $this->customParameters;
    }
}
