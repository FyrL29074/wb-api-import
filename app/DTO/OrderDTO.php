<?php

namespace App\DTO;

class OrderDTO
{
    public function __construct(
        public ?string $g_number,
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?int $barcode,
        public ?string $total_price,
        public ?int $discount_percent,
        public ?string $warehouse_name,
        public ?string $oblast,
        public ?int $income_id,
        public ?string $odid,
        public ?int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public ?bool $is_cancel,
        public ?string $cancel_dt
    ) {}

    public static function fromArray(array $item): self
    {
        return new self(
            $item['g_number'],
            $item['date'],
            $item['last_change_date'],
            $item['supplier_article'],
            $item['tech_size'],
            (int) $item['barcode'],
            $item['total_price'],
            (int) $item['discount_percent'],
            $item['warehouse_name'],
            $item['oblast'],
            (int) $item['income_id'],
            $item['odid'] ?? '',
            (int) $item['nm_id'],
            $item['subject'],
            $item['category'],
            $item['brand'],
            (bool) $item['is_cancel'],
            $item['cancel_dt'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'g_number' => $this->g_number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'total_price' => $this->total_price,
            'discount_percent' => $this->discount_percent,
            'warehouse_name' => $this->warehouse_name,
            'oblast' => $this->oblast,
            'income_id' => $this->income_id,
            'odid' => $this->odid,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'is_cancel' => $this->is_cancel,
            'cancel_dt' => $this->cancel_dt,
        ];
    }
}
