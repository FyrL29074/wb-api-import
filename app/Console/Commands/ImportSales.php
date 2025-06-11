<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseImportCommand;

class ImportSales extends BaseImportCommand
{
    protected $signature = 'import:sales';
    protected $description = 'Импорт sales — все страницы';

    protected function getApiPath(): string
    {
        return '/api/sales';
    }

    protected function getDTOClass(): string
    {
        return \App\DTO\SaleDTO::class;
    }

    protected function getModelClass(): string
    {
        return \App\Models\Sale::class;
    }

    protected function getDateFrom(): string
    {
        return '0001-01-01';
    }
}
