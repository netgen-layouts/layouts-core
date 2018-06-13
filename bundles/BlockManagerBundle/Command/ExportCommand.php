<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Command;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Transfer\Output\SerializerInterface;
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
     * @var \Netgen\BlockManager\Transfer\Output\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    public function __construct(SerializerInterface $serializer, Filesystem $fileSystem)
    {
        $this->serializer = $serializer;
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
                $hash = $this->serializer->serializeLayouts($ids);
                break;
            case 'rule':
                $hash = $this->serializer->serializeRules($ids);
                break;
            default:
                throw new RuntimeException(sprintf('Unhandled type %s', $type));
        }

        $json = json_encode($hash, JSON_PRETTY_PRINT);

        $file = $input->getArgument('file');

        $file !== null ?
            $this->fileSystem->dumpFile(trim($file), $json) :
            $output->writeln((string) $json);

        return 0;
    }
}
