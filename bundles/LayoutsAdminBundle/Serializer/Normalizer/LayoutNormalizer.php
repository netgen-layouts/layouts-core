<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use DateTimeInterface;
use Generator;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function array_map;
use function count;

final class LayoutNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private LayoutService $layoutService,
        private BlockService $blockService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Layout\Layout $layout */
        $layout = $data->value;
        $layoutType = $layout->layoutType;

        $availableLocales = [];
        foreach ($layout->availableLocales as $locale) {
            $availableLocales[$locale] = Locales::getName($locale);
        }

        $normalizedData = [
            'id' => $layout->id->toString(),
            'type' => $layoutType->identifier,
            'published' => $layout->isPublished,
            'has_published_state' => $this->layoutService->layoutExists($layout->id, Status::Published),
            'created_at' => $layout->created->format(DateTimeInterface::ATOM),
            'updated_at' => $layout->modified->format(DateTimeInterface::ATOM),
            'has_archived_state' => false,
            'archive_created_at' => null,
            'archive_updated_at' => null,
            'shared' => $layout->isShared,
            'name' => $layout->name,
            'description' => $layout->description,
            'main_locale' => $layout->mainLocale,
            'available_locales' => $availableLocales,
            'zones' => $this->normalizer->normalize($this->getZones($layout, $layoutType), $format, $context),
        ];

        try {
            $archivedLayout = $this->layoutService->loadLayoutArchive($layout->id);

            $normalizedData['has_archived_state'] = true;
            $normalizedData['archive_created_at'] = $archivedLayout->created->format(DateTimeInterface::ATOM);
            $normalizedData['archive_updated_at'] = $archivedLayout->modified->format(DateTimeInterface::ATOM);
        } catch (NotFoundException) {
            // Do nothing
        }

        return $normalizedData;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Layout;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Returns the array with layout zones.
     *
     * @return \Generator<array<string, mixed>>
     */
    private function getZones(Layout $layout, LayoutTypeInterface $layoutType): Generator
    {
        foreach ($layout as $zoneIdentifier => $zone) {
            $linkedZone = $zone->linkedZone;

            $normalizedData = [
                'identifier' => $zoneIdentifier,
                'name' => $this->getZoneName($zone, $layoutType),
                'block_ids' => array_map('strval', $this->blockService->loadZoneBlocks($zone)->getBlockIds()),
                'allowed_block_definitions' => $this->getAllowedBlocks(
                    $zone,
                    $layoutType,
                ),
                'linked_layout_id' => null,
                'linked_zone_identifier' => null,
            ];

            if ($linkedZone instanceof Zone) {
                $normalizedData['linked_layout_id'] = $linkedZone->layoutId->toString();
                $normalizedData['linked_zone_identifier'] = $linkedZone->identifier;
            }

            yield $normalizedData;
        }
    }

    /**
     * Returns provided zone name.
     */
    private function getZoneName(Zone $zone, LayoutTypeInterface $layoutType): string
    {
        if ($layoutType->hasZone($zone->identifier)) {
            return $layoutType->getZone($zone->identifier)->name;
        }

        return $zone->identifier;
    }

    /**
     * Returns all allowed block definitions from provided zone or
     * true if all block definitions are allowed.
     *
     * @return string[]|true
     */
    private function getAllowedBlocks(Zone $zone, LayoutTypeInterface $layoutType): array|true
    {
        if ($layoutType->hasZone($zone->identifier)) {
            $layoutTypeZone = $layoutType->getZone($zone->identifier);

            if (count($layoutTypeZone->allowedBlockDefinitions) > 0) {
                return $layoutTypeZone->allowedBlockDefinitions;
            }
        }

        return true;
    }
}
