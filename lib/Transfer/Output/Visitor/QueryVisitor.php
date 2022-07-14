<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

use function iterator_to_array;
use function ksort;

/**
 * Query value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Collection\Query
 *
 * @implements \Netgen\Layouts\Transfer\Output\VisitorInterface<\Netgen\Layouts\API\Values\Collection\Query>
 */
final class QueryVisitor implements VisitorInterface
{
    private CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    public function accept(object $value): bool
    {
        return $value instanceof Query;
    }

    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'id' => $value->getId()->toString(),
            'is_translatable' => $value->isTranslatable(),
            'is_always_available' => $value->isAlwaysAvailable(),
            'main_locale' => $value->getMainLocale(),
            'available_locales' => $value->getAvailableLocales(),
            'parameters' => $this->visitParameters($value),
            'query_type' => $value->getQueryType()->getType(),
        ];
    }

    /**
     * Visit the given $query parameters into hash representation.
     *
     * @return array<string, mixed>
     */
    private function visitParameters(Query $query): array
    {
        $parametersByLanguage = [
            $query->getLocale() => iterator_to_array(
                $this->visitTranslationParameters($query),
            ),
        ];

        foreach ($query->getAvailableLocales() as $availableLocale) {
            if ($availableLocale === $query->getLocale()) {
                continue;
            }

            $translatedQuery = $this->collectionService->loadQuery(
                $query->getId(),
                [$availableLocale],
                false,
            );

            $parametersByLanguage[$availableLocale] = iterator_to_array(
                $this->visitTranslationParameters($translatedQuery),
            );
        }

        ksort($parametersByLanguage);

        return $parametersByLanguage;
    }

    /**
     * Return parameters for the given $query.
     *
     * @return \Generator<string, mixed>
     */
    private function visitTranslationParameters(Query $query): Generator
    {
        foreach ($query->getParameters() as $parameter) {
            $definition = $parameter->getParameterDefinition();
            $exportedValue = $definition->getType()->export($definition, $parameter->getValue());

            yield $parameter->getName() => $exportedValue;
        }
    }
}
