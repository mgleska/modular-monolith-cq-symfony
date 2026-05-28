<?php
declare(strict_types=1);

namespace Module\Product\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProductQuantityRepository::class)]
#[ORM\Table(name: ProductQuantity::TABLE)]
#[ORM\UniqueConstraint(fields: ['storeId', 'productId'])]
class ProductQuantity
{
    use TimestampableEntity;
    public const string TABLE = 'prd_product_quantity';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $storeId;

    #[ORM\Column]
    private int $productId;

    #[ORM\Column]
    private int $quantity;

    public static function factory(int $storeId, int $productId, int $quantity, ?int $id = null): self
    {
        $entity = new self();
        $entity->storeId = $storeId;
        $entity->productId = $productId;
        $entity->quantity = $quantity;
        $entity->id = $id;

        return $entity;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
