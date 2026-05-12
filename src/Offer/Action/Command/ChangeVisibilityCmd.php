<?php

declare(strict_types=1);

namespace Module\Offer\Action\Command;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Module\Offer\Action\Dto\Admin\ChangeVisibilityDto;
use Module\Offer\Support\OfferRepository;
use Module\Shared\Exception\AppEntityVersionException;
use Module\Shared\Exception\AppValidationException;

class ChangeVisibilityCmd
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly OfferRepository $repository,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function handle(ChangeVisibilityDto $dto): void
    {
        $this->em->beginTransaction();

        try {
            $offer = $this->repository->find($dto->id, LockMode::PESSIMISTIC_WRITE);

            if ($offer === null) {
                throw new AppValidationException('id', 'Offer not found');
            }

            if ($offer->getVersion() !== $dto->version) {
                throw new AppEntityVersionException();
            }

            $offer->setVisible($dto->visible);
            $offer->setVersion($offer->getVersion() + 1);
            $this->repository->save($offer, true);

            $this->em->commit();
        }
        catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
