<?php

namespace Netgen\BlockManager\Transfer\Output\Visitor;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query as QueryValue;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Visitor;

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

    public function accept($value)
    {
        return $value instanceof QueryValue;
    }

    public function visit($query, Visitor $subVisitor = null)
    {
        if ($subVisitor === null) {
            throw new RuntimeException('Implementation requires sub-visitor');
        }

        /* @var \Netgen\BlockManager\API\Values\Collection\Query $query */

        return array(
            'id' => $query->getId(),
            'is_translatable' => $query->isTranslatable(),
            'is_always_available' => $query->isAlwaysAvailable(),
            'main_locale' => $query->getMainLocale(),
            'available_locales' => $query->getAvailableLocales(),
            'parameters' => $this->visitParameterValues($query, $subVisitor),
            'query_type' => $query->getQueryType()->getType(),
        );
    }

    /**
     * Visit the given $query parameters into hash representation.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     *
     * @return array
     */
    private function visitParameterValues(QueryValue $query, Visitor $subVisitor)
    {
        $parameterValuesByLanguage = array(
            $query->getLocale() => $this->visitBlockTranslationParameterValues($query, $subVisitor),
        );

        foreach ($query->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $query->getLocale()) {
                continue;
            }

            $translatedBlock = $this->collectionService->loadQuery(
                $query->getId(),
                array($availableLocale),
                false
            );

            $parameterValuesByLanguage[$availableLocale] = $this->visitBlockTranslationParameterValues(
                $translatedBlock,
                $subVisitor
            );
        }

        ksort($parameterValuesByLanguage);

        return $parameterValuesByLanguage;
    }

    /**
     * Return parameters for the given $query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \Netgen\BlockManager\Transfer\Output\Visitor $subVisitor
     *
     * @return mixed|null
     */
    private function visitBlockTranslationParameterValues(QueryValue $query, Visitor $subVisitor)
    {
        $hash = array();
        $parameterValues = $query->getParameters();

        if (empty($parameterValues)) {
            return null;
        }

        foreach ($parameterValues as $parameterValue) {
            $hash[$parameterValue->getName()] = $subVisitor->visit($parameterValue);
        }

        return $hash;
    }
}
