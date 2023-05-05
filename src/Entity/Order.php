<?php

namespace App\Entity;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\Date]
    private \DateTimeInterface $date;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $payment_method;

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $payment_status;

    #[ORM\JoinTable(name: 'orders_products')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Product::class)]
    private Collection $products;

    public function __construct() {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    public function getPaymentStatus(): string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): void
    {
        $this->payment_status = $payment_status;
    }

    public function addProduct(Product $product): void
    {
        $this->products->add($product);
    }

    public function getProducts()
    {
        return $this->products;
    }
}
