<?php
// src/Fetcher.php
declare(strict_types=1);

namespace App\Services;

use App\Services\HttpClient;
use App\Traits\Logger;

class Fetcher
{
    use Logger;

    private string $scriptPath;
    private HttpClient $client;
    public function __construct(private string $method)
    {
        $this->client = new HttpClient();
        $this->scriptPath = dirname(__DIR__,2) . "/{$_ENV['SCRIPTS_PATH']}/{$_ENV['PUPPETEER_SCRIPT']}";
    }

    private function curl(string $url): string|false
    {
        return $this->client->fetch($url);
    }

    private function puppeteer(string $url): string|false
    {
        $cmd = escapeshellcmd('node ' . $this->scriptPath) . ' ' . escapeshellarg($url);
        $output = shell_exec($cmd);
        return $output === null ? false : $output;
    }

    public function fetch(string $url): string|false
    {
        if ($this->method == CliOptions::FETCH_CURL) {
            return $this->curl($url);
        } else {
            return $this->puppeteer($url);
        }
    }
}
