<?php
declare(strict_types=1);

namespace Module\Store\Access\Console;

use Module\Store\Action\Command\ImportStoresCmd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/** @codeCoverageIgnore */
#[AsCommand(
    name: 'store:import',
    description: 'Command to import stores from external dictionary (API)'
)]
class ImportStoresCon
{
    public function __construct(
        private readonly ImportStoresCmd $action,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('Importing stores from external dictionary...');
        $this->action->handle();
        $output->writeln('Stores imported successfully!');

        return Command::SUCCESS;
    }
}
