<?php

namespace App\Entity;

use App\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'float',
        message: 'The value {{ value }} is not a valid {{ type }}.',
    )]
    #[Assert\PositiveOrZero]
    private $price = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = $price;
    }
}
