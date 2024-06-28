<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Cli;

use App\Application\AcquireLogs;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'log:parse',
    description: 'Parses the log file',
    hidden: false
)]
class LogParseCommand extends Command
{
    public function __construct(
        private readonly AcquireLogs $acquireLogs,
        private readonly int $readAheadLinesNum,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Cli entrypoint to parse logs file.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $numParsedLines = $this->acquireLogs->process($this->readAheadLinesNum);
            $output->writeln("<info>$numParsedLines lines were successfuly parsed</info>");

            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln('<error>Something went wrong!</error>');
            return Command::FAILURE;
        }
    }
}
