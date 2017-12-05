<?php

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Command to export Netgen Layouts entities.
 */
final class ExportCommand extends Command
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
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    public function __construct(
        Serializer $serializer,
        LayoutService $layoutService,
        LayoutResolverService $layoutResolverService,
        Filesystem $fileSystem
    ) {
        $this->serializer = $serializer;
        $this->layoutService = $layoutService;
        $this->layoutResolverService = $layoutResolverService;
        $this->fileSystem = $fileSystem;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ngbm:export')
            ->setDescription('Exports Netgen Layouts entities')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of the entity to export')
            ->addArgument('ids', InputArgument::REQUIRED, 'Comma-separated list of IDs of the entities to export')
            ->addArgument('file', InputArgument::OPTIONAL, 'If specified, exported entities will be written to provided file')
            ->setHelp('The command <info>%command.name%</info> exports Netgen Layouts entities.');
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
                throw new RuntimeException(sprintf('Unhandled type %s', $type));
        }

        $json = json_encode($hash, JSON_PRETTY_PRINT);

        $file = trim($input->getArgument('file'));
        !empty($file) ?
            $this->fileSystem->dumpFile($file, $json) :
            $output->writeln($json);
    }

    /**
     * Serialize all layouts form the given array of layout IDs.
     *
     * @param string[]|int[] $ids
     *
     * @return array
     */
    private function serializeLayouts(array $ids)
    {
        $layouts = array();

        foreach ($ids as $id) {
            $layouts[] = $this->serializer->serializeLayout(
                $this->layoutService->loadLayout($id)
            );
        }

        return $layouts;
    }

    /**
     * Serialize all rules form the given array of rule IDs.
     *
     * @param string[]|int[] $ids
     *
     * @return array
     */
    private function serializeRules(array $ids)
    {
        $rules = array();

        foreach ($ids as $id) {
            $rules[] = $this->serializer->serializeRule(
                $this->layoutResolverService->loadRule($id)
            );
        }

        return $rules;
    }
}
