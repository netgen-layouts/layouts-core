<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Command;

use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Transfer\Output\SerializerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command to export Netgen Layouts entities.
 */
final class ExportCommand extends Command
{
    /**
     * @var \Netgen\Layouts\Transfer\Output\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $io;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        // Parent constructor call is mandatory in commands registered as services
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Exports Netgen Layouts entities')
            ->addArgument('type', InputArgument::REQUIRED, 'Type of the entity to export')
            ->addArgument('ids', InputArgument::REQUIRED, 'Comma-separated list of UUIDs of the entities to export')
            ->setHelp('The command <info>%command.name%</info> exports Netgen Layouts entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $this->io = new SymfonyStyle($input, $output);

        $type = $input->getArgument('type');
        if (!is_string($type)) {
            throw new RuntimeException('Invalid import type.');
        }

        $ids = $input->getArgument('ids');
        if (!is_array($ids)) {
            $ids = explode(',', $ids ?? '');
        }

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
        if (!is_string($json)) {
            throw new RuntimeException('Serialization failed.');
        }

        $this->io->writeln($json);

        return 0;
    }
}
