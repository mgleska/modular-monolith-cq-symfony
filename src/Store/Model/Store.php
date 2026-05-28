<?php
declare(strict_types=1);

namespace Module\Store\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Module\Shared\Constants;

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

    public static function factory(string $externalId, string $name, string $address, ?int $id = null): self
    {
        $entity = new self();
        $entity->externalId = $externalId;
        $entity->name = $name;
        $entity->address = $address;
        $entity->id = $id;

        return $entity;
    }

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
