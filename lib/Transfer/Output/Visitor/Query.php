<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;

/**
 * Query value visitor.
 *
 * @see \Netgen\BlockManager\API\Values\Collection\Query
 */
final class Query extends Visitor
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    private $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function accept($value): bool
    {
        return $value instanceof QueryValue;
    }

    public function visit($query, VisitorInterface $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Query $query */

        return [
            'id' => $query->getId(),
            'is_translatable' => $query->isTranslatable(),
            'is_always_available' => $query->isAlwaysAvailable(),
            'main_locale' => $query->getMainLocale(),
            'available_locales' => $query->getAvailableLocales(),
            'parameters' => $this->visitParameters($query, $subVisitor),
            'query_type' => $query->getQueryType()->getType(),
        ];
    }

    /**
     * Visit the given $query parameters into hash representation.
     */
    private function visitParameters(QueryValue $query, VisitorInterface $subVisitor): array
    {
        $parametersByLanguage = [
            $query->getLocale() => $this->visitTranslationParameters($query, $subVisitor),
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

            $parametersByLanguage[$availableLocale] = $this->visitTranslationParameters(
                $translatedQuery,
                $subVisitor
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $query.
     */
    private function visitTranslationParameters(QueryValue $query, VisitorInterface $subVisitor): array
    {
        $hash = [];

        foreach ($query->getParameters() as $parameter) {
            $hash[$parameter->getName()] = $subVisitor->visit($parameter);
        }

        return $hash;
    }
}
