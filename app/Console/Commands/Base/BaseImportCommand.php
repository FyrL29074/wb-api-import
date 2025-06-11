<?php

namespace App\Console\Commands\Base;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

abstract class BaseImportCommand extends Command
{
    abstract protected function getApiPath(): string;
    abstract protected function getDTOClass(): string;
    abstract protected function getModelClass(): string;
    abstract protected function getDateFrom(): string;

    public function handle()
    {
        $protocol = env('API_PROTOCOL');
        $host = env('API_HOST');
        $port = env('API_PORT');
        $key = env('API_KEY');

        $baseUrl = "{$protocol}://{$host}:{$port}";
        $dateFrom = $this->getDateFrom();

        $this->importPages($baseUrl, $key, $dateFrom);
    }

    protected function importPages($baseUrl, $key, $dateFrom)
    {
        $response = Http::get("{$baseUrl}{$this->getApiPath()}", [
            'dateFrom' => $dateFrom,
            'dateTo' => '9999-01-01',
            'page' => 1,
            'limit' => 500,
            'key' => $key
        ]);

        $meta = $response->json('meta');
        $lastPage = $meta['last_page'] ?? 1;

        $this->info("Всего страниц для импорта: {$lastPage}");

        for ($page = 1; $page <= $lastPage; $page++) {
            $this->importPage($baseUrl, $key, $dateFrom, $page);
        }
    }

    protected function importPage($baseUrl, $key, $dateFrom, $page)
    {
        $this->info("Импортируем страницу {$page}...");

        $response = Http::get("{$baseUrl}{$this->getApiPath()}", [
            'dateFrom' => $dateFrom,
            'dateTo' => '9999-01-01',
            'page' => $page,
            'limit' => 500,
            'key' => $key
        ]);

        if (!$response->successful()) {
            $this->error("Ошибка при запросе страницы {$page}: HTTP " . $response->status());
            $this->error("Ответ API: " . $response->body());
            return;
        }

        $data = $response->json('data') ?? [];

        if (empty($data)) {
            $this->info("Страница {$page} пустая.");
        } else {
            $this->info("Импортировано " . count($data) . " записей с страницы {$page}.");
        }

        $batch = [];

        $dtoClass = $this->getDTOClass();
        foreach ($data as $item) {
            $dto = $dtoClass::fromArray($item);
            $batch[] = $dto->toArray();
        }

        $this->insertBatchSafe($batch, $this->getModelClass());

        unset($batch);
        unset($data);
        $response = null;
        gc_collect_cycles();
    }

    protected function insertBatchSafe(array $batch, string $modelClass, int $chunkSize = 100): void
    {
        if (empty($batch)) {
            return;
        }

        foreach (array_chunk($batch, $chunkSize) as $chunk) {
            $modelClass::insert($chunk);
        }
    }
}
