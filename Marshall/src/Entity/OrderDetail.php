<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    private ?Product $product = null;
    private ?Order $order = null;
    private $total;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    private ?Order $orderid = null;

    #[ORM\Column]
    private ?int $quantity = null;

   
   


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getOrderid(): ?Order
    {
        return $this->orderid;
    }

    public function setOrderid(?Order $orderid): static
    {
        $this->orderid = $orderid;

        return $this;
    }

    

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): static
{
    $this->quantity = $quantity ?? 0;

    return $this;
}
public function getOrder(): ?Order
{
    return $this->order;
}

public function setOrder(?Order $order): self
{
    $this->order = $order;

    return $this;
}
public function getTotal(): ?int
{
    return $this->total;
}

public function setTotal(int $total): self
{
    $this->total = $total;

    return $this;
}




}
