<?php
declare(strict_types=1);

namespace Module\Store\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Module\Shared\Constants;
use Module\Store\Support\StoreRepository;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ORM\Table(name: 'str_store')]
#[Gedmo\SoftDeleteable]
class Store
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: Constants::STORE_EXTERNAL_ID_MAX_LENGTH, unique: true)]
    private string $externalId;

    #[ORM\Column(length: 250)]
    private string $name;

    #[ORM\Column(length: 250)]
    private string $address;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }
}
