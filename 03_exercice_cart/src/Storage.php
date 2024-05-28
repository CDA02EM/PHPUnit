<?php
namespace App;
use App\Product;

class Storage {
    private array $products = [];

    public function add(Product $product) {
        $this->products[] = $product;
    }

    public function remove(string $productName) {
        foreach($this->products as $key => $product) {
            if($productName === $product->getName()) {
                unset($key);
                $this->products = array_values($this->products);
                break;
            }
        }
    }

    public function reset() {
        $this->products = [];
    }

    public function calculateTotal() {
        $total = 0;
        foreach($this->products as $product) {
            $total += $product->getTotalPrice();
        }
        return $total;
    }

    public function getProducts() {
        return $this->products;
    }
}