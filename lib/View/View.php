<?php

declare(strict_types=1);

namespace Netgen\Layouts\View;

use Netgen\Layouts\Exception\View\ViewException;
use Symfony\Component\HttpFoundation\Response;

use function array_key_exists;
use function get_debug_type;

abstract class View implements ViewInterface
{
    final public ?string $context = null {
        get => $this->context;
        set {
            if ($value === null) {
                throw new ViewException('$context property cannot be set to null.');
            }

            $this->context = $value;
        }
    }

    final public ?string $fallbackContext = null {
        get => $this->fallbackContext;
        set {
            if ($value === null) {
                throw new ViewException('$fallbackContext property cannot be set to null.');
            }

            $this->fallbackContext = $value;
        }
    }

    final public ?string $template = null {
        get => $this->template;
        set {
            if ($value === null) {
                throw new ViewException('$template property cannot be set to null.');
            }

            $this->template = $value;
        }
    }

    final public ?Response $response = null {
        get => $this->response;
        set {
            if ($value === null) {
                throw new ViewException('$response property cannot be set to null.');
            }

            $this->response = $value;
        }
    }

    /**
     * @var array<string, mixed>
     */
    public array $parameters {
        get => [...$this->customParameters, ...$this->internalParameters];
    }

    /**
     * @var array<string, mixed>
     */
    private array $internalParameters = [];

    /**
     * @var array<string, mixed>
     */
    private array $customParameters = [];

    final public function hasParameter(string $identifier): bool
    {
        return array_key_exists($identifier, $this->parameters);
    }

    final public function getParameter(string $identifier): mixed
    {
        if (!$this->hasParameter($identifier)) {
            throw ViewException::parameterNotFound($identifier, get_debug_type($this));
        }

        return $this->parameters[$identifier];
    }

    final public function addParameter(string $parameterName, mixed $parameterValue): static
    {
        $this->customParameters[$parameterName] = $parameterValue;

        return $this;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    final public function addParameters(array $parameters): static
    {
        $this->customParameters = [...$this->customParameters, ...$parameters];

        return $this;
    }

    final protected function addInternalParameter(string $parameterName, mixed $parameterValue): static
    {
        $this->internalParameters[$parameterName] = $parameterValue;

        return $this;
    }
}
