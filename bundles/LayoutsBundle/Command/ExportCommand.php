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

use function array_combine;
use function array_fill;
use function count;
use function explode;
use function is_array;
use function is_string;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

/**
 * Command to export Netgen Layouts entities.
 */
final class ExportCommand extends Command
{
    private SerializerInterface $serializer;

    private SymfonyStyle $io;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
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

        $hash = $this->serializer->serialize(array_combine($ids, array_fill(0, count($ids), $type)));

        $this->io->writeln(json_encode($hash, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return 0;
    }
}
