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
use Symfony\Component\Validator\Constraints;

final class LayoutValidator extends Validator
{
    /**
     * Validates the provided layout create struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutCreateStruct(LayoutCreateStruct $layoutCreateStruct): void
    {
        $layoutName = is_string($layoutCreateStruct->name) ?
            trim($layoutCreateStruct->name) :
            $layoutCreateStruct->name;

        $this->validate(
            $layoutName,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'name'
        );

        if ($layoutCreateStruct->description !== null) {
            $this->validate(
                $layoutCreateStruct->description,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
            );
        }

        $this->validate(
            $layoutCreateStruct->layoutType,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => LayoutTypeInterface::class]),
            ],
            'layoutType'
        );

        $this->validateLocale($layoutCreateStruct->mainLocale, 'mainLocale');

        if ($layoutCreateStruct->shared !== null) {
            $this->validate(
                $layoutCreateStruct->shared,
                [
                    new Constraints\Type(['type' => 'bool']),
                ],
                'shared'
            );
        }
    }

    /**
     * Validates the provided layout update struct.
     *
     * @throws \Netgen\Layouts\Exception\Validation\ValidationException If the validation failed
     */
    public function validateLayoutUpdateStruct(LayoutUpdateStruct $layoutUpdateStruct): void
    {
        $layoutName = is_string($layoutUpdateStruct->name) ?
            trim($layoutUpdateStruct->name) :
            $layoutUpdateStruct->name;

        if ($layoutName !== null) {
            $this->validate(
                $layoutName,
                [
                    new Constraints\NotBlank(),
                    new Constraints\Type(['type' => 'string']),
                ],
                'name'
            );
        }

        if ($layoutUpdateStruct->description !== null) {
            $this->validate(
                $layoutUpdateStruct->description,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
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
        $layoutName = is_string($layoutCopyStruct->name) ?
            trim($layoutCopyStruct->name) :
            $layoutCopyStruct->name;

        $this->validate(
            $layoutName,
            [
                new Constraints\NotBlank(),
                new Constraints\Type(['type' => 'string']),
            ],
            'name'
        );

        if ($layoutCopyStruct->description !== null) {
            $this->validate(
                $layoutCopyStruct->description,
                [
                    new Constraints\Type(['type' => 'string']),
                ],
                'description'
            );
        }
    }

    /**
     * Validates zone mappings for changing the provided layout type.
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
                        $targetLayoutType->getIdentifier()
                    )
                );
            }

            if (!is_array($oldZones)) {
                throw ValidationException::validationFailed(
                    'zoneMappings',
                    sprintf(
                        'The list of mapped zones for "%s" zone must be an array.',
                        $newZone
                    )
                );
            }

            $oldLayoutZones = [];

            foreach ($oldZones as $oldZone) {
                if (in_array($oldZone, $seenZones, true)) {
                    throw ValidationException::validationFailed(
                        'zoneMappings',
                        sprintf(
                            'Zone "%s" is specified more than once.',
                            $oldZone
                        )
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
                                $newZone
                            )
                        );
                    }
                }
            }
        }
    }
}
