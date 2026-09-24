<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Models;

class Product
{
    public function __construct(
        public ?int $id = null,
        public ?int $categoryId = null,
        public ?int $supplierId = null,
        public string $name = '',
        public string $sku = '',
        public float $price = 0.0,
        public int $stock = 0,
        public ?string $description = null,
        public ?float $discountPercentage = null,
        public ?float $rating = null,
        public ?string $thumbnail = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $categoryName = null,
        public ?string $supplierName = null,
    ) {}

    /**
     * Create Product instance from database row array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            categoryId: isset($data['category_id']) && $data['category_id'] !== '' ? (int) $data['category_id'] : null,
            supplierId: isset($data['supplier_id']) && $data['supplier_id'] !== '' ? (int) $data['supplier_id'] : null,
            name: (string) ($data['name'] ?? ''),
            sku: (string) ($data['sku'] ?? ''),
            price: isset($data['price']) ? (float) $data['price'] : 0.0,
            stock: isset($data['stock']) ? (int) $data['stock'] : 0,
            description: isset($data['description']) && trim((string) $data['description']) !== '' ? (string) $data['description'] : null,
            discountPercentage: isset($data['discount_percentage']) && $data['discount_percentage'] !== '' 
                ? (float) $data['discount_percentage'] 
                : (isset($data['discountPercentage']) && $data['discountPercentage'] !== '' ? (float) $data['discountPercentage'] : null),
            rating: isset($data['rating']) && $data['rating'] !== '' ? (float) $data['rating'] : null,
            thumbnail: isset($data['thumbnail']) && trim((string) $data['thumbnail']) !== '' ? (string) $data['thumbnail'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
            categoryName: isset($data['category_name']) ? (string) $data['category_name'] : null,
            supplierName: isset($data['supplier_name']) ? (string) $data['supplier_name'] : null,
        );
    }

    /**
     * Convert Product to array for database persistence.
     */
    public function toArray(): array
    {
        return [
            'id'                  => $this->id,
            'category_id'         => $this->categoryId,
            'supplier_id'         => $this->supplierId,
            'name'                => $this->name,
            'sku'                 => $this->sku,
            'price'               => $this->price,
            'stock'               => $this->stock,
            'description'         => $this->description,
            'discount_percentage' => $this->discountPercentage,
            'rating'              => $this->rating,
            'thumbnail'           => $this->thumbnail,
        ];
    }

    /**
     * Format price to Indonesian Rupiah.
     */
    public function getFormattedPrice(): string
    {
        return format_rupiah($this->price);
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
