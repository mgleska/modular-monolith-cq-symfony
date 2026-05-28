<?php
declare(strict_types=1);

namespace Module\Offer\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Module\Shared\Constants;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(name: 'ofr_offer')]
#[ORM\UniqueConstraint(fields: ['storeId', 'productEan'])]
class Offer
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $version = 0;

    #[ORM\Column]
    private int $storeId;

    #[ORM\Column(length: Constants::OFFER_EXTERNAL_ID_MAX_LENGTH)]
    private string $externalId;

    #[ORM\Column(length: Constants::EAN_MAX_LENGTH)]
    private string $productEan;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $productName = null;

    #[ORM\Column]
    private int $price;

    #[ORM\Column(nullable: true)]
    private ?int $lowestPrice = null;

    #[ORM\Column(options: ['default' => true])]
    private bool $visible = true;

    #[ORM\Column(nullable: true)]
    private ?int $productId = null;

    public static function factory(
        int $storeId,
        string $externalId,
        string $productEan,
        ?string $productName,
        int $price,
        ?int $lowestPrice = null,
        bool $visible = true,
        ?int $productId = null,
        int $version = 0,
        ?int $id = null,
    ): self {
        $entity = new self();
        $entity->id = $id;
        $entity->version = $version;
        $entity->storeId = $storeId;
        $entity->externalId = $externalId;
        $entity->productEan = $productEan;
        $entity->productName = $productName;
        $entity->price = $price;
        $entity->lowestPrice = $lowestPrice;
        $entity->visible = $visible;
        $entity->productId = $productId;

        return $entity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getStoreId(): int
    {
        return $this->storeId;
    }

    public function setStoreId(int $storeId): static
    {
        $this->storeId = $storeId;

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

    public function getProductEan(): string
    {
        return $this->productEan;
    }

    public function setProductEan(string $productEan): static
    {
        $this->productEan = $productEan;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getLowestPrice(): ?int
    {
        return $this->lowestPrice;
    }

    public function setLowestPrice(?int $lowestPrice): static
    {
        $this->lowestPrice = $lowestPrice;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }
}
