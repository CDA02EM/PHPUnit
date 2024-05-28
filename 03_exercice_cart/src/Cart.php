<?php
namespace App;

class Cart {
    public function __construct(private Storage $storage) {}

    public function getProducts() {
        return $this->storage->getProducts();
    }

    public function addProduct(Product $product): void 
    {
        $this->storage->add($product);
    }

    public function removeProduct(string $productName): void 
    {
        $this->storage->remove($productName);
    }

    public function reset(): void 
    {
        $this->storage->reset();
    }

    public function total(): float
    {
        return $this->storage->calculateTotal();
    }
}