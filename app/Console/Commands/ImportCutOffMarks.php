<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicInfo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;

class ImportCutOffMarks extends Command
{
    protected $signature = 'import:cutoff-marks';

    protected $description = 'Importar notas de corte desde notasdecorte.es';

    public function handle(): int
    {
        $this->info('Iniciando importación de notas de corte...');

        try {
            $sitemap = 'https://notasdecorte.es/sitemap.xml';
            $pages = [$sitemap];

            $response = Http::get($sitemap);
            if ($response->ok()) {
                $xml = simplexml_load_string($response->body());
                foreach ($xml->sitemap as $item) {
                    $pages[] = (string) $item->loc;
                }
            }

            foreach ($pages as $pageUrl) {
                $this->info('Procesando '.$pageUrl);
                $xmlResponse = Http::get($pageUrl);
                if (!$xmlResponse->ok()) {
                    continue;
                }
                $xml = simplexml_load_string($xmlResponse->body());
                foreach ($xml->url as $url) {
                    $this->scrapePage((string) $url->loc);
                }
            }

            $this->info('Importación completada');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Error al importar notas de corte: '.$e->getMessage());
            $this->error('Error al importar notas de corte');
            return Command::FAILURE;
        }
    }

    private function scrapePage(string $url): void
    {
        $html = Http::get($url)->body();
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $rows = $xpath->query('//table[contains(@class,"views-table")]/tbody/tr');
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            if ($cells->length < 4) {
                continue;
            }
            $degree = trim($cells->item(0)->textContent);
            $university = trim($cells->item(1)->textContent);
            $city = trim($cells->item(2)->textContent);
            $mark = floatval(str_replace(',', '.', trim($cells->item(3)->textContent)));

            AcademicInfo::updateOrCreate(
                [
                    'university_name' => $university,
                    'degree_name' => $degree,
                    'year' => date('Y'),
                    'type' => 'notas-corte',
                ],
                [
                    'city' => $city,
                    'cut_off_mark' => $mark,
                ]
            );
        }
    }
}
