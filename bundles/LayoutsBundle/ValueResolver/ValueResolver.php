<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver;

use Netgen\Layouts\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

use function in_array;
use function is_a;

abstract class ValueResolver implements ValueResolverInterface
{
    final protected const string STATUS_PUBLISHED = 'published';

    final protected const string STATUS_DRAFT = 'draft';

    final protected const string STATUS_ARCHIVED = 'archived';

    private const ROUTE_STATUS_PARAM = '_nglayouts_status';

    /**
     * @return iterable<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_a($argument->getType() ?? '', $this->getSupportedClass(), true)) {
            return [];
        }

        if ($argument->getName() !== $this->getDestinationAttributeName()) {
            return [];
        }

        $values = [];

        foreach ($this->getSourceAttributeNames() as $attributeName) {
            if (!$request->attributes->has($attributeName)) {
                return [];
            }

            $values[$attributeName] = $request->attributes->get($attributeName) ?? '';

            if ($values[$attributeName] === '') {
                throw new InvalidArgumentException(
                    $attributeName,
                    'Required request attribute is empty.',
                );
            }
        }

        $routeStatusParam = $request->attributes->get(self::ROUTE_STATUS_PARAM);
        $queryPublishedParam = $request->query->get('published');

        $values['status'] = self::STATUS_DRAFT;
        if (in_array($routeStatusParam, [self::STATUS_PUBLISHED, self::STATUS_DRAFT, self::STATUS_ARCHIVED], true)) {
            $values['status'] = $routeStatusParam;
        } elseif ($queryPublishedParam === 'true') {
            $values['status'] = self::STATUS_PUBLISHED;
        }

        if ($request->attributes->has('locale')) {
            $values['locale'] = $request->attributes->get('locale');
        }

        yield $this->loadValue($values);
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
     * @param array<string, mixed> $values
     */
    abstract public function loadValue(array $values): object;
}
