<?php

namespace App\Models;

use App\Core\Contracts\ModelInterface;
use App\Services\StorageInterface;


readonly class ProductRepository implements ModelInterface
{
    public function __construct(private readonly StorageInterface $storage)
    {
    }

    public function list(): array
    {
        return $this->storage->getAll('products') ?? [];
    }

    public function create(array $data): void
    {
        $products = $this->list();
        $id = count($products) + 1;
        $products[] = ['id' => $id] + $data;
        $this->storage->set('products', $products);
    }

    public function delete(int $id): void
    {
        $products = array_filter(
            $this->list(),
            fn($product) => $product['id'] !== $id
        );
        $this->storage->set('products', array_values($products));
    }
}