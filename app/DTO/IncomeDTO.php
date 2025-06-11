<?php

namespace App\DTO;

class IncomeDTO
{
    public function __construct(
        public ?int $income_id,
        public ?string $number,
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?int $barcode,
        public ?int $quantity,
        public ?string $total_price,
        public ?string $date_close,
        public ?string $warehouse_name,
        public ?int $nm_id
    ) {}

    public static function fromArray(array $item): self
    {
        return new self(
            (int) $item['income_id'],
            $item['number'],
            $item['date'],
            $item['last_change_date'],
            $item['supplier_article'],
            $item['tech_size'],
            (int) $item['barcode'],
            (int) $item['quantity'],
            $item['total_price'],
            $item['date_close'],
            $item['warehouse_name'],
            (int) $item['nm_id']
        );
    }

    public function toArray(): array
    {
        return [
            'income_id' => $this->income_id,
            'number' => $this->number,
            'date' => $this->date,
            'last_change_date' => $this->last_change_date,
            'supplier_article' => $this->supplier_article,
            'tech_size' => $this->tech_size,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'total_price' => $this->total_price,
            'date_close' => $this->date_close,
            'warehouse_name' => $this->warehouse_name,
            'nm_id' => $this->nm_id,
        ];
    }
}
