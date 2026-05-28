<?php
declare(strict_types=1);

namespace Module\Product\Access\Console;

use Module\Product\Action\Command\ImportQuantityCmd;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/** @codeCoverageIgnore */
#[AsCommand(
    name: 'product:quantity',
    description: 'Command to import quantities for each products in all stores (API)'
)]
class ImportProductsQuantityCon
{
    public function __construct(
        private readonly ImportQuantityCmd $action,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('Importing product quantity...');
        $this->action->handle();
        $output->writeln('Product quantity imported successfully!');

        return Command::SUCCESS;
    }
}
