<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver;

use Netgen\Layouts\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use ValueError;

use function is_a;

abstract class ValueResolver implements ValueResolverInterface
{
    /**
     * @return iterable<mixed>
     */
    final public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType() ?? '', $this->getSupportedClass(), true)) {
            return [];
        }

        if ($argument->getName() !== $this->getDestinationAttributeName()) {
            return [];
        }

        $parameters = [];

        foreach ($this->getSourceAttributeNames() as $attributeName) {
            if (!$request->attributes->has($attributeName)) {
                return [];
            }

            $parameters[$attributeName] = $request->attributes->get($attributeName, '');

            if ($parameters[$attributeName] === '') {
                throw new InvalidArgumentException(
                    $attributeName,
                    'Required request attribute is empty.',
                );
            }
        }

        $routeStatusParam = $request->attributes->getString('_nglayouts_status');
        $queryPublishedParam = $request->query->getString('published');

        $parameters['status'] = Status::Draft;

        try {
            $parameters['status'] = Status::from($routeStatusParam);
        } catch (ValueError) {
            if ($queryPublishedParam === 'true') {
                $parameters['status'] = Status::Published;
            }
        }

        if ($request->attributes->has('locale')) {
            $parameters['locale'] = $request->attributes->getString('locale');
        }

        yield $this->loadValue($parameters);
    }

    /**
     * Returns source attribute name.
     *
     * @return string[]
     */
    abstract public function getSourceAttributeNames(): array;

    /**
     * Returns destination attribute name.
     */
    abstract public function getDestinationAttributeName(): string;

    /**
     * Returns the supported class.
     *
     * @return class-string
     */
    abstract public function getSupportedClass(): string;

    /**
     * Returns the value.
     *
     * @param array<string, mixed> $parameters
     */
    abstract public function loadValue(array $parameters): object;
}
