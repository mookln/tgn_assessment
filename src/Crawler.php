<?php

declare(strict_types=1);

namespace App;

use App\Database;
use App\Services\UrlService;
use App\Traits\Logger;
use PDOException;

final class Crawler
{
    use Logger;
    private Database $db;
    private UrlService $urlService;
    private array $urls;

    public function __construct(string $urlsFile, string $method)
    {
        $this->db = new Database();
        $this->urlService = new UrlService();
        //get url list
        $this->urls = $this->urlService->loadUrls($urlsFile);
    }

    public function run(): void
    {
        try {
            //migrate database
            $this->db->migrate();
        } catch (PDOException $e) {
            $this->error('DB migration failed: ' . $e->getMessage());
            echo "Database error. Check log.\n";
            return;
        }

        //iterate url list
        foreach ($this->urls as $url) {

            echo "Processing: $url \n";
            $this->info("processing $url");
            //normalize url
            //save duplicates
            //parse
            //save
        }


        //exit;
        echo "Done. Check data/products.sqlite and data/log.txt\n";
    }
}
