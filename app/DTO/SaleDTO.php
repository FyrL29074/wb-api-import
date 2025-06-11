<?php

namespace App\DTO;

class SaleDTO
{
    public function __construct(
        public ?string $g_number,
        public ?string $date,
        public ?string $last_change_date,
        public ?string $supplier_article,
        public ?string $tech_size,
        public ?int $barcode,
        public ?string $total_price,
        public ?string $discount_percent,
        public ?bool $is_supply,
        public ?bool $is_realization,
        public ?string $promo_code_discount,
        public ?string $warehouse_name,
        public ?string $country_name,
        public ?string $oblast_okrug_name,
        public ?string $region_name,
        public ?int $income_id,
        public ?string $sale_id,
        public ?string $odid,
        public ?string $spp,
        public ?string $for_pay,
        public ?string $finished_price,
        public ?string $price_with_disc,
        public ?int $nm_id,
        public ?string $subject,
        public ?string $category,
        public ?string $brand,
        public ?string $is_storno
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
            $item['discount_percent'],
            (bool) $item['is_supply'],
            (bool) $item['is_realization'],
            $item['promo_code_discount'] ?? null,
            $item['warehouse_name'],
            $item['country_name'],
            $item['oblast_okrug_name'],
            $item['region_name'],
            (int) $item['income_id'],
            $item['sale_id'],
            $item['odid'] ?? null,
            $item['spp'],
            $item['for_pay'],
            $item['finished_price'],
            $item['price_with_disc'],
            (int) $item['nm_id'],
            $item['subject'],
            $item['category'],
            $item['brand'],
            $item['is_storno'] ?? null
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
            'is_supply' => $this->is_supply,
            'is_realization' => $this->is_realization,
            'promo_code_discount' => $this->promo_code_discount,
            'warehouse_name' => $this->warehouse_name,
            'country_name' => $this->country_name,
            'oblast_okrug_name' => $this->oblast_okrug_name,
            'region_name' => $this->region_name,
            'income_id' => $this->income_id,
            'sale_id' => $this->sale_id,
            'odid' => $this->odid,
            'spp' => $this->spp,
            'for_pay' => $this->for_pay,
            'finished_price' => $this->finished_price,
            'price_with_disc' => $this->price_with_disc,
            'nm_id' => $this->nm_id,
            'subject' => $this->subject,
            'category' => $this->category,
            'brand' => $this->brand,
            'is_storno' => $this->is_storno,
        ];
    }
}
