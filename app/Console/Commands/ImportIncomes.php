<?php

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseImportCommand;

class ImportIncomes extends BaseImportCommand
{
    protected $signature = 'import:incomes';
    protected $description = 'Импорт incomes — все страницы';

    protected function getApiPath(): string
    {
        return '/api/incomes';
    }

    protected function getDTOClass(): string
    {
        return \App\DTO\IncomeDTO::class;
    }

    protected function getModelClass(): string
    {
        return \App\Models\Income::class;
    }

    protected function getDateFrom(): string
    {
        return '0001-01-01';
    }
}
