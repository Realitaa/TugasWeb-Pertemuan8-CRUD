<?php

declare(strict_types=1);

namespace Realitaa\PhpVite\Models;

class Supplier
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
    ) {}

    /**
     * Create Supplier instance from database array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int) $data['id'] : null,
            name: (string) ($data['name'] ?? ''),
            email: isset($data['email']) && $data['email'] !== '' ? (string) $data['email'] : null,
            phone: isset($data['phone']) && $data['phone'] !== '' ? (string) $data['phone'] : null,
            address: isset($data['address']) && $data['address'] !== '' ? (string) $data['address'] : null,
        );
    }

    /**
     * Convert Supplier to associative array.
     */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'address' => $this->address,
        ];
    }
}
