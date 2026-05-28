<?php
declare(strict_types=1);

namespace Module\Customer\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Module\Customer\Action\Enum\CustomerStatusEnum;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'cst_customer')]
class Customer implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $selectedStore = 0;

    #[ORM\Column(type: Types::ENUM, options: ['default' => CustomerStatusEnum::ACTIVE])]
    private CustomerStatusEnum $status = CustomerStatusEnum::ACTIVE;

    #[ORM\Column(length: 250)]
    private string $name;

    public static function factory(string $name, int $selectedStore, CustomerStatusEnum $status, ?int $id = null): self
    {
        $entity = new self();
        $entity->name = $name;
        $entity->selectedStore = $selectedStore;
        $entity->status = $status;
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

    public function getStatus(): CustomerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CustomerStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSelectedStore(): int
    {
        return $this->selectedStore;
    }

    public function setSelectedStore(int $selectedStore): static
    {
        $this->selectedStore = $selectedStore;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }

    /** @noinspection PhpMultipleClassDeclarationsInspection */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
