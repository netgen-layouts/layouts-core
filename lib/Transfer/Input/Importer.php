<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler;
use Netgen\Layouts\Transfer\Input\DataHandler\RuleDataHandler;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Netgen\Layouts\Transfer\Output\Visitor\LayoutVisitor;
use Netgen\Layouts\Transfer\Output\Visitor\RuleVisitor;
use Ramsey\Uuid\Uuid;
use Throwable;
use Traversable;
use function file_get_contents;
use function json_decode;
use const JSON_THROW_ON_ERROR;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
final class Importer implements ImporterInterface
{
    /**
     * The path to the root schema directory.
     */
    private const SCHEMA_FILE = __DIR__ . '/../../../resources/schemas/import.json';

    /**
     * @var \Netgen\Layouts\Transfer\Input\JsonValidatorInterface
     */
    private $jsonValidator;

    /**
     * @var \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     */
    private $layoutDataHandler;

    /**
     * @var \Netgen\Layouts\Transfer\Input\DataHandler\RuleDataHandler
     */
    private $ruleDataHandler;

    public function __construct(
        JsonValidatorInterface $jsonValidator,
        LayoutDataHandler $layoutDataHandler,
        RuleDataHandler $ruleDataHandler
    ) {
        $this->jsonValidator = $jsonValidator;
        $this->layoutDataHandler = $layoutDataHandler;
        $this->ruleDataHandler = $ruleDataHandler;
    }

    public function importData(string $data): Traversable
    {
        $schema = (string) file_get_contents(self::SCHEMA_FILE);
        $this->jsonValidator->validateJson($data, $schema);

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['entities'] as $entityData) {
            try {
                if ($entityData['__type'] === LayoutVisitor::ENTITY_TYPE) {
                    $layout = $this->layoutDataHandler->createLayout($entityData);
                    yield new SuccessResult($entityData['__type'], $entityData, $layout->getId(), $layout);
                } elseif ($entityData['__type'] === RuleVisitor::ENTITY_TYPE) {
                    $rule = $this->ruleDataHandler->createRule($entityData);
                    yield new SuccessResult($entityData['__type'], $entityData, $rule->getId(), $rule);
                }
            } catch (Throwable $t) {
                yield new ErrorResult($entityData['__type'], $entityData, Uuid::fromString($entityData['id']), $t);
            }
        }
    }
}
