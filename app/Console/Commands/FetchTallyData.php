<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchTallyData extends Command
{
    protected $signature = 'tally:fetch {--continuous : Run continuously every 5 seconds}';
    protected $description = 'Fetch XML data from Tally and store as JSON';

    public function handle()
    {
        if ($this->option('continuous')) {
            $this->info('Starting continuous Tally data fetch (every 5 seconds)...');
            $this->info('Press Ctrl+C to stop.');

            while (true) {
                $this->fetchData();
                sleep(5); // Wait for 5 seconds
            }
        } else {
            return $this->fetchData();
        }
    }

    /**
     * Fetch and process data from Tally
     */
    private function fetchData(): int
    {
        $tallyUrl = 'http://127.0.0.1:9000';

        $xmlRequest = '
        <ENVELOPE>
            <HEADER>
                <VERSION>1</VERSION>
                <TALLYREQUEST>Export</TALLYREQUEST>
                <TYPE>Data</TYPE>
                <ID>Day Book</ID>
            </HEADER>
            <BODY>
                <DESC>
                    <STATICVARIABLES>
                        <SVEXPORTFORMAT>$$SysName:XML</SVEXPORTFORMAT>
                    </STATICVARIABLES>
                </DESC>
            </BODY>
        </ENVELOPE>';

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'text/xml'])
                ->withBody($xmlRequest, 'text/xml')
                ->post($tallyUrl);

            if (!$response->successful()) {
                Log::error('Tally fetch failed: ' . $response->status());
                $this->error('Tally not responding: ' . $response->status());
                return 1;
            }

            $xmlData = $response->body();

            // Convert XML to JSON
            $jsonData = $this->convertXmlToJson($xmlData);

            if ($jsonData === null) {
                Log::error('Failed to convert XML to JSON');
                $this->error('XML to JSON conversion failed');
                return 1;
            }

            // Save JSON with timestamp and milliseconds for uniqueness
            $filename = 'tally_' . now()->format('Y-m-d_H-i-s_v') . '.json';

            // Create directory if it doesn't exist
            $directory = storage_path('app/tally/json');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save JSON file
            file_put_contents($directory . '/' . $filename, json_encode($jsonData, JSON_PRETTY_PRINT));

            Log::info('Tally data saved as JSON: ' . $filename);
            $this->info("[" . now()->format('H:i:s') . "] Saved JSON: {$filename}");

            return 0;

        } catch (\Exception $e) {
            Log::error('Tally fetch exception: ' . $e->getMessage());
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Convert XML string to JSON array
     */
    private function convertXmlToJson(string $xmlString): ?array
    {
        try {
            // Load XML string
            $xml = simplexml_load_string($xmlString);

            if ($xml === false) {
                throw new \Exception('Invalid XML format');
            }

            // Convert XML to array
            $json = json_encode($xml);
            $array = json_decode($json, true);

            return $array;

        } catch (\Exception $e) {
            Log::error('XML to JSON conversion error: ' . $e->getMessage());
            return null;
        }
    }
}
