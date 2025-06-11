<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Stock;

class ImportStocks extends Command
{
    protected $signature = 'import:stocks';
    protected $description = 'Импорт stocks — все страницы';

    public function handle()
    {
        $protocol = env('API_PROTOCOL');
        $host = env('API_HOST');
        $port = env('API_PORT');
        $key = env('API_KEY');

        $baseUrl = "{$protocol}://{$host}:{$port}";
        
        $dateFrom = date('Y-m-d');

        $this->importPages($baseUrl, $key, $dateFrom);
    }

    private function importPages($baseUrl, $key, $dateFrom)
    {
        $response = Http::get("{$baseUrl}/api/stocks", [
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

    private function importPage($baseUrl, $key, $dateFrom, $page)
    {
        $this->info("Импортируем страницу {$page}...");

        $maxRetries = 3;
        $attempt = 0;
        $success = false;

        while ($attempt < $maxRetries && !$success) {
            $attempt++;
            $this->info("Попытка {$attempt}...");

            $response = Http::get("{$baseUrl}/api/stocks", [
                'dateFrom' => $dateFrom,
                'dateTo' => '9999-01-01',
                'page' => $page,
                'limit' => 500,
                'key' => $key
            ]);

            if ($response->successful()) {
                $success = true;

                $this->info("Страница {$page}, HTTP " . $response->status());
                $this->info("Body длина: " . strlen($response->body()));

                $json = $response->json();
                $this->info("Ключи ответа: " . implode(', ', array_keys($json ?? [])));

                $data = $response->json('data') ?? [];

                if (empty($data)) {
                    $this->info("Страница {$page} пустая.");
                } else {
                    $this->info("Импортировано " . count($data) . " записей с страницы {$page}.");
                }

                foreach ($data as $item) {
                    Stock::create([
                        'date' => $item['date'],
                        'last_change_date' => $item['last_change_date'] ?? null,
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'barcode' => $item['barcode'],
                        'quantity' => $item['quantity'],
                        'is_supply' => $item['is_supply'],
                        'is_realization' => $item['is_realization'],
                        'quantity_full' => $item['quantity_full'],
                        'warehouse_name' => $item['warehouse_name'],
                        'in_way_to_client' => $item['in_way_to_client'],
                        'in_way_from_client' => $item['in_way_from_client'],
                        'nm_id' => $item['nm_id'],
                        'subject' => $item['subject'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'sc_code' => $item['sc_code'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                    ]);
                }
            } else {
                $this->error("Ошибка при запросе страницы {$page}: HTTP " . $response->status());
                $this->error("Ответ API: " . $response->body());

                if ($attempt < $maxRetries) {
                    $this->info("Ждём 10 секунд перед повтором...");
                    sleep(10);
                } else {
                    $this->error("Максимальное количество попыток ({$maxRetries}) исчерпано. Пропускаем страницу {$page}.");
                }
            }
        }
    }
}
