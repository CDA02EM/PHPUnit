## Exercice: Créer un panier

### Partie 1: Première Classe

On vas premièrement créer une classe `Cart` qui comportera toutes les responsabilitées.

Cette classe aura les méthodes :
- addProduct()
- removeProduct()
- reset()
- total()

Un produit sera représenté par un objet comportant les propriétées "price", "quantity" et "name".

```php
class Cart {

}

$cart = new Cart();

$product1 = [
    "productName" => 'Laptop',
    "price" => '1200.0',
    "quantity" => '2',
];
$product2 = [
    "productName" => 'Phone',
    "price" => '800.0',
    "quantity" => '1',
];

$cart->addProduct($product1);
$cart->addProduct($product2);

echo "Total du panier : $" . $cart->total(); // Affiche : Total du panier : $3200.0
```

### Partie 1.2: Création des tests unitaire pour la classe `Cart`

Créer la classe CartTest avec PHPUnit

### Partie 2: Décomposition de la classe

Dans cette partie nous allons séparer les responsabilitées de notre classe `Cart` en la décomposant en trois classes : `Cart`, `Product` et `Storage`.

Cette décomposition permettra de séparer les responsabilités et rendra le code plus modulaire.

Classe `Cart`

```php
<?php

class Cart
{
    private Storage $storage;

    public function __construct(Storage $storage)
    {
        // ...code
    }

    public function addProduct(Product $product): void
    {
        // ...code
    }

    public function removeProduct(string $productName): void
    {
        // ...code
    }

    public function reset(): void
    {
        // ...code
    }

    public function total(): float
    {
        return $this->storage->calculateTotal();
    }
}
```

Classe `Product`

```php
<?php

class Product
{
    public function __construct(private string $name, float $price, int $quantity) {}

    public function getName(): string
    {
        // ...code
    }

    public function getTotalPrice(): float
    {
        // ...code
    }
}
```

Class `Storage`

```php
<?php

class Storage
{
    private array $products = [];

    public function add(Product $product): void
    {
        // ...code
    }

    public function remove(string $productName): void
    {
        // ...code
    }

    public function reset(): void
    {
        // ...code
    }

    public function calculateTotal(): float
    {
        // ...code
    }
}
```

### Partie 2.2: Complété les tests unitaires

Mettez à jour votre classe `Cart` et créé les autres classes de tests