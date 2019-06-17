<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Query value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Query
 */
final class QueryVisitor implements VisitorInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function accept($value): bool
    {
        return $value instanceof Query;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Collection\Query $value
     * @param \Netgen\Layouts\Transfer\Output\Visitor\AggregateVisitor $aggregateVisitor
     *
     * @return mixed
     */
    public function visit($value, AggregateVisitor $aggregateVisitor)
    {
        return [
            'id' => $value->getId()->toString(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'parameters' => $this->visitParameters($value, $aggregateVisitor),
            'query_type' => $value->getQueryType()->getType(),
        ];
    }

    /**
     * Visit the given $query parameters into hash representation.
     */
    private function visitParameters(Query $query, AggregateVisitor $aggregateVisitor): array
    {
        $parametersByLanguage = [
            $query->getLocale() => iterator_to_array(
                $this->visitTranslationParameters($query, $aggregateVisitor)
            ),
        ];

        foreach ($query->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $query->getLocale()) {
                continue;
            }

            $translatedQuery = $this->collectionService->loadQuery(
                $query->getId(),
                [$availableLocale],
                false
            );

            $parametersByLanguage[$availableLocale] = iterator_to_array(
                $this->visitTranslationParameters(
                    $translatedQuery,
                    $aggregateVisitor
                )
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $query.
     */
    private function visitTranslationParameters(Query $query, AggregateVisitor $aggregateVisitor): Generator
    {
        foreach ($query->getParameters() as $parameter) {
            yield $parameter->getName() => $aggregateVisitor->visit($parameter);
        }
    }
}
