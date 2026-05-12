<?php
declare(strict_types=1);

namespace Module\Offer\Access\Console;

use Module\Offer\Action\Command\ImportOffersCmd;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'offer:import',
    description: 'Command to import offers for given store.'
)]
class ImportOffersCon
{
    public function __construct(
        private readonly ImportOffersCmd $action,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(
        #[Argument(description: 'store exteral ID', name: 'storeRid')] string $storeRid,
        OutputInterface $output,
    ): int {
        $output->writeln("Importing offers for store with rid: $storeRid ...");
        $this->action->handle($storeRid);
        $output->writeln('Offers imported successfully!');

        return Command::SUCCESS;
    }
}
