<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Transfer\Output\Serializer;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Export Block Manager entities.
 */
class ExportCommand extends Command
{
    /**
     * @var \Netgen\BlockManager\Transfer\Output\Serializer
     */
    private $serializer;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutService
     */
    private $layoutService;

    /**
     * @var \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverService;

    /**
     * {@inheritdoc}
     *
     * @param \Netgen\BlockManager\Transfer\Output\Serializer $serializer
     * @param \Netgen\BlockManager\API\Service\LayoutService $layoutService
     * @param \Netgen\BlockManager\API\Service\LayoutResolverService $layoutResolverService
     */
    public function __construct(
        Serializer $serializer,
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService
    ) {
        $this->serializer = $serializer;
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('netgen_block_manager:export')
            ->setDescription('Exports Block Manager entities')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Type of the entity to export'
            )
            ->addArgument(
                'ids',
                InputArgument::REQUIRED,
                'Comma-separated list of IDs of the entities to export'
            )
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> exports Block Manager entities.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $ids = explode(',', $input->getArgument('ids'));

        switch ($type) {
            case 'layout':
                $hash = $this->serializeLayouts($ids);
                break;
            case 'rule':
                $hash = $this->serializeRules($ids);
                break;
            default:
                throw new RuntimeException("Unhandled type '{$type}'");
        }

        $output->writeln(json_encode($hash, JSON_PRETTY_PRINT));
    }

    /**
     * Serialize all Layouts form the given array of Layout $ids.
     *
     * @param string[]|int[] $ids
     *
     * @return array
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     */
    private function serializeLayouts(array $ids)
    {
        $layouts = [];

        foreach ($ids as $id) {
            $layouts[] = $this->serializer->serializeLayout(
                $this->layoutService->loadLayout($id)
            );
        }

        return $layouts;
    }

    /**
     * Serialize all Rules form the given array of Rule $ids.
     *
     * @param string[]|int[] $ids
     *
     * @return array
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException
     */
    private function serializeRules(array $ids)
    {
        $rules = [];

        foreach ($ids as $id) {
            $rules[] = $this->serializer->serializeRule(
                $this->layoutResolverService->loadRule($id)
            );
        }

        return $rules;
    }
}
