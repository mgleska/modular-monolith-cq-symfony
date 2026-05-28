<?php
declare(strict_types=1);

namespace Module\Product\Access\Console;

use Module\Product\Action\Command\ImportProductsCmd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/** @codeCoverageIgnore */
#[AsCommand(
    name: 'product:import',
    description: 'Command to import products from external dictionary (API)'
)]
class ImportProductsCon
{
    public function __construct(
        private readonly ImportProductsCmd $action,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('Importing products from external dictionary...');
        $this->action->handle();
        $output->writeln('Products imported successfully!');

        return Command::SUCCESS;
    }
}
