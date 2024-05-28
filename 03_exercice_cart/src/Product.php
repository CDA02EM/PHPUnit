<?php
namespace App;

class Product {
    public function __construct(private string $name, private float $price, private int $quantity) {}
    
    public function getName(): string
    {
        return $this->name;
    }

    public function getTotalPrice(): float 
    {
        return $this->price * $this->quantity;
    }
}