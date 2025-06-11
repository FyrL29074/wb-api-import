<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseImportCommand;

class ImportOrders extends BaseImportCommand
{
    protected $signature = 'import:orders';
    protected $description = 'Импорт orders — все страницы';

    protected function getApiPath(): string
    {
        return '/api/orders';
    }

    protected function getDTOClass(): string
    {
        return \App\DTO\OrderDTO::class;
    }

    protected function getModelClass(): string
    {
        return \App\Models\Order::class;
    }

    protected function getDateFrom(): string
    {
        return '0001-01-01';
    }
}
