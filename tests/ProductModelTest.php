<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Realitaa\PhpVite\Models\Product;

class ProductModelTest extends TestCase
{
    public function testProductModelInstantiationAndAttributes(): void
    {
        $data = [
            'id'                  => 10,
            'name'                => 'Test Product',
            'sku'                 => 'TEST-SKU-001',
            'price'               => 49000.0,
            'stock'               => 15,
            'description'         => 'Test product description',
            'discount_percentage' => 12.5,
            'rating'              => 4.75,
            'thumbnail'           => 'https://example.com/item.png',
        ];

        $product = Product::fromArray($data);

        $this->assertSame(10, $product->id);
        $this->assertSame('Test Product', $product->name);
        $this->assertSame('TEST-SKU-001', $product->sku);
        $this->assertSame(49000.0, $product->price);
        $this->assertSame(15, $product->stock);
        $this->assertSame('Test product description', $product->description);
        $this->assertSame(12.5, $product->discountPercentage);
        $this->assertSame(4.75, $product->rating);
        $this->assertSame('https://example.com/item.png', $product->thumbnail);

        $array = $product->toArray();
        $this->assertSame(12.5, $array['discount_percentage']);
        $this->assertSame(4.75, $array['rating']);
        $this->assertSame('https://example.com/item.png', $array['thumbnail']);
    }

    public function testProductModelHandlesNullOptionalFields(): void
    {
        $data = [
            'name'  => 'Minimal Product',
            'sku'   => 'MIN-001',
            'price' => 10000.0,
            'stock' => 5,
        ];

        $product = Product::fromArray($data);

        $this->assertNull($product->description);
        $this->assertNull($product->discountPercentage);
        $this->assertNull($product->rating);
        $this->assertNull($product->thumbnail);
    }
}
