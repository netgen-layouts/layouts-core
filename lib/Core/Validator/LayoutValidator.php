<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Validator;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\LayoutCopyStruct;
use Netgen\Layouts\API\Values\Layout\LayoutCreateStruct;
use Netgen\Layouts\API\Values\Layout\LayoutUpdateStruct;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Exception\Validation\ValidationException;
use Netgen\Layouts\Layout\Type\LayoutTypeInterface;
use Netgen\Layouts\Validator\ValidatorTrait;
use Symfony\Component\Validator\Constraints;

use function count;
use function in_array;
use function is_array;
use function sprintf;
use function trim;

final class LayoutValidator
{
    use ValidatorTrait;

    /**
     * Validates the provided layout create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct): void
    {
        if (!isset($layoutCreateStruct->name)) {
            throw ValidationException::validationFailed('name', sprintf('"name" is required in %s', LayoutCreateStruct::class));
        }

        if (!isset($layoutCreateStruct->layoutType)) {
            throw ValidationException::validationFailed('layoutType', sprintf('"layoutType" is required in %s', LayoutCreateStruct::class));
        }

        if (!isset($layoutCreateStruct->mainLocale)) {
            throw ValidationException::validationFailed('mainLocale', sprintf('"mainLocale" is required in %s', LayoutCreateStruct::class));
        }

        $this->validate(
            trim($layoutCreateStruct->name),
            [
                new Constraints\NotBlank(),
            ],
            'name',
        );

        $this->validateLocale($layoutCreateStruct->mainLocale, 'mainLocale');
    }

    /**
     * Validates the provided layout update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutUpdateStruct(LayoutUpdateStruct $layoutUpdateStruct): void
    {
        if ($layoutUpdateStruct->name !== null) {
            $this->validate(
                trim($layoutUpdateStruct->name),
                [
                    new Constraints\NotBlank(),
                ],
                'name',
            );
        }
    }

    /**
     * Validates the provided layout copy struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutCopyStruct(LayoutCopyStruct $layoutCopyStruct): void
    {
        if (!isset($layoutCopyStruct->name)) {
            throw ValidationException::validationFailed('name', sprintf('"name" is required in %s', LayoutCopyStruct::class));
        }

        $this->validate(
            trim($layoutCopyStruct->name),
            [
                new Constraints\NotBlank(),
            ],
            'name',
        );
    }

    /**
     * Validates zone mappings for changing the provided layout type.
     *
     * @param array<string, string[]> $zoneMappings
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateChangeLayoutType(Layout $layout, LayoutTypeInterface $targetLayoutType, array $zoneMappings, bool $preserveSharedZones = true): void
    {
        $seenZones = [];
        foreach ($zoneMappings as $newZone => $oldZones) {
            if (!$targetLayoutType->hasZone($newZone)) {
                throw ValidationException::validationFailed(
                    'zoneMappings',
                    sprintf(
                        'Zone "%s" does not exist in "%s" layout type.',
                        $newZone,
                        $targetLayoutType->getIdentifier(),
                    ),
                );
            }

            if (!is_array($oldZones)) {
                throw ValidationException::validationFailed(
                    'zoneMappings',
                    sprintf(
                        'The list of mapped zones for "%s" zone must be an array.',
                        $newZone,
                    ),
                );
            }

            $oldLayoutZones = [];

            foreach ($oldZones as $oldZone) {
                if (in_array($oldZone, $seenZones, true)) {
                    throw ValidationException::validationFailed(
                        'zoneMappings',
                        sprintf(
                            'Zone "%s" is specified more than once.',
                            $oldZone,
                        ),
                    );
                }

                $seenZones[] = $oldZone;
                $oldLayoutZones[] = $layout->getZone($oldZone);
            }

            if ($preserveSharedZones && count($oldLayoutZones) > 1) {
                foreach ($oldLayoutZones as $oldZone) {
                    if ($oldZone->getLinkedZone() instanceof Zone) {
                        throw ValidationException::validationFailed(
                            'zoneMappings',
                            sprintf(
                                'When preserving shared layout zones, mapping for zone "%s" needs to be 1:1.',
                                $newZone,
                            ),
                        );
                    }
                }
            }
        }
    }
}
