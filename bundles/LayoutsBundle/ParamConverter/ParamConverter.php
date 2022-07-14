<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter;

use Netgen\Layouts\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

use function in_array;
use function is_a;

abstract class ParamConverter implements ParamConverterInterface
{
    protected const STATUS_PUBLISHED = 'published';

    protected const STATUS_DRAFT = 'draft';

    protected const STATUS_ARCHIVED = 'archived';

    private const ROUTE_STATUS_PARAM = '_nglayouts_status';

    public function apply(Request $request, ParamConverterConfiguration $configuration): bool
    {
        $sourceAttributeNames = $this->getSourceAttributeNames();
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            if (!$request->attributes->has($sourceAttributeName)) {
                return false;
            }
        }

        $values = [];
        foreach ($sourceAttributeNames as $sourceAttributeName) {
            $values[$sourceAttributeName] = $request->attributes->get($sourceAttributeName) ?? '';

            if ($values[$sourceAttributeName] === '') {
                if ($configuration->isOptional()) {
                    return false;
                }

                throw new InvalidArgumentException(
                    $sourceAttributeName,
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

        $request->attributes->set(
            $this->getDestinationAttributeName(),
            $this->loadValue($values),
        );

        return true;
    }

    public function supports(ParamConverterConfiguration $configuration): bool
    {
        return is_a($configuration->getClass(), $this->getSupportedClass(), true);
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
