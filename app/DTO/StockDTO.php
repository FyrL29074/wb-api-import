<?php

namespace App\DTO;

class StockDTO
{
    public function __construct(
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?int $barcode,
        public ?int $quantity,
        public ?bool $is_supply,
        public ?bool $is_realization,
        public ?int $quantity_full,
        public ?string $warehouse_name,
        public ?int $in_way_to_client,
        public ?int $in_way_from_client,
        public ?int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public ?int $sc_code,
        public ?string $price,
        public ?string $discount
    ) {}

    public static function fromArray(array $item): self
    {
        return new self(
            $item['date'],
            $item['last_change_date'] ?? null,
            $item['supplier_article'],
            $item['tech_size'],
            (int) $item['barcode'],
            (int) $item['quantity'],
            (bool) $item['is_supply'],
            (bool) $item['is_realization'],
            (int) $item['quantity_full'],
            $item['warehouse_name'],
            (int) $item['in_way_to_client'],
            (int) $item['in_way_from_client'],
            (int) $item['nm_id'],
            $item['subject'],
            $item['category'],
            $item['brand'],
            (int) $item['sc_code'],
            $item['price'],
            $item['discount']
        );
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'is_supply' => $this->is_supply,
            'is_realization' => $this->is_realization,
            'quantity_full' => $this->quantity_full,
            'warehouse_name' => $this->warehouse_name,
            'in_way_to_client' => $this->in_way_to_client,
            'in_way_from_client' => $this->in_way_from_client,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'sc_code' => $this->sc_code,
            'price' => $this->price,
            'discount' => $this->discount,
        ];
    }
}
