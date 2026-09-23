<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Models;

class Category
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
    ) {}

    /**
     * Create Category instance from database array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: (string) ($data['name'] ?? ''),
        );
    }

    /**
     * Convert Category to associative array.
     */
    public function toArray(): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
