<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class ImportOrders extends Command
{
    protected $signature = 'import:orders';
    protected $description = 'Импорт orders — все страницы';

    public function handle()
    {
        $protocol = env('API_PROTOCOL');
        $host = env('API_HOST');
        $port = env('API_PORT');
        $key = env('API_KEY');

        $baseUrl = "{$protocol}://{$host}:{$port}";

        $dateFrom = '0001-01-01';

        $this->importPages($baseUrl, $key, $dateFrom);
    }

    private function importPages($baseUrl, $key, $dateFrom)
    {
        $response = Http::get("{$baseUrl}/api/orders", [
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

            $response = Http::get("{$baseUrl}/api/orders", [
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
                    Order::create([
                        'g_number' => $item['g_number'],
                        'date' => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size' => $item['tech_size'],
                        'barcode' => $item['barcode'],
                        'total_price' => $item['total_price'],
                        'discount_percent' => $item['discount_percent'],
                        'warehouse_name' => $item['warehouse_name'],
                        'oblast' => $item['oblast'],
                        'income_id' => $item['income_id'],
                        'odid' => $item['odid'] ?? null,
                        'nm_id' => $item['nm_id'],
                        'subject' => $item['subject'],
                        'category' => $item['category'],
                        'brand' => $item['brand'],
                        'is_cancel' => $item['is_cancel'],
                        'cancel_dt' => $item['cancel_dt'] ?? null,
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
