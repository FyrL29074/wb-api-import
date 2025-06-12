<?php

namespace App\Console\Commands\Base;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;

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
        Log::info("Импортируем страницу {$page}...");
        
        $response = $this->sendRequestWithRetries(
            "{$baseUrl}{$this->getApiPath()}",
            [
                'dateFrom' => $dateFrom,
                'dateTo' => '9999-01-01',
                'page' => $page,
                'limit' => 500,
                'key' => $key
            ]
        );

        if (!$response) {
            return;
        }

        if (!$response->successful()) {
            $this->error("Ошибка при запросе страницы {$page}: HTTP " . $response->status());
            $this->error("Ответ API: " . $response->body());
            Log::error("Ошибка при запросе страницы {$page}: HTTP " . $response->status());
            return;
        }

        $data = $response->json('data') ?? [];

        if (empty($data)) {
            $this->info("Страница {$page} пустая.");
            Log::warning("Страница {$page} пустая.");
        } else {
            $this->info("Импортировано " . count($data) . " записей с страницы {$page}.");
            Log::info("Импортируем страницу {$page}...");
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

    protected function sendRequestWithRetries(string $url, array $params, int $maxRetries = 3, int $retryDelay = 10): ?Response
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $this->info("Попытка {$attempt} запроса к {$url} (page={$params['page']})");
            Log::info("Попытка {$attempt} запроса к {$url} (page={$params['page']})");

            $response = Http::get($url, $params);

            if ($response->successful()) {
                return $response;
            }

            $this->error("Ошибка HTTP {$response->status()} при попытке {$attempt} (page={$params['page']})");
            Log::error("Ошибка HTTP {$response->status()} при попытке {$attempt} (page={$params['page']})");

            if ($attempt < $maxRetries) {
                $this->info("Ждём {$retryDelay} секунд перед повтором...");
                Log::info("Ждём {$retryDelay} секунд перед повтором...");
                sleep($retryDelay);
            }
        }

        $this->error("Максимальное количество попыток ({$maxRetries}) исчерпано. Пропускаем страницу {$params['page']}.");
        Log::error("Максимальное количество попыток ({$maxRetries}) исчерпано. Пропускаем страницу {$params['page']}.");

        return null;
    }
}
