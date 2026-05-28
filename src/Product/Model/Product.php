<?php
declare(strict_types=1);

namespace Module\Product\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Module\Shared\Constants;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'prd_product')]
class Product
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: Constants::EAN_MAX_LENGTH, unique: true)]
    private string $ean;

    #[ORM\Column(length: 200)]
    private string $name;

    #[ORM\Column(length: Constants::URL_MAX_LENGTH, nullable: true)]
    private ?string $imageUrl = null;

    public static function factory(string $ean, string $name, ?string $imageUrl = null, ?int $id = null): self
    {
        $entity = new self();
        $entity->ean = $ean;
        $entity->name = $name;
        $entity->imageUrl = $imageUrl;
        $entity->id = $id;

        return $entity;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEan(): string
    {
        return $this->ean;
    }

    public function setEan(string $ean): static
    {
        $this->ean = $ean;

        return $this;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
