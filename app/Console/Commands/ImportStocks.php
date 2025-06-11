<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseImportCommand;

class ImportStocks extends BaseImportCommand
{
    protected $signature = 'import:stocks';
    protected $description = 'Импорт stocks — все страницы';

    protected function getApiPath(): string
    {
        return '/api/stocks';
    }

    protected function getDTOClass(): string
    {
        return \App\DTO\StockDTO::class;
    }

    protected function getModelClass(): string
    {
        return \App\Models\Stock::class;
    }

    protected function getDateFrom(): string
    {
        return now()->format('Y-m-d');
    }
}
