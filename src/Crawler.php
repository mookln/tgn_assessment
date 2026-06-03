<?php

declare(strict_types=1);

namespace App;

use App\Database;
use App\Product\ProductParser;
use App\Services\Fetcher;
use App\Services\UrlService;
use App\Traits\Logger;
use InvalidArgumentException;
use PDOException;
use RuntimeException;

final class Crawler
{
    use Logger;
    private Database $db;
    private UrlService $urlService;
    private Fetcher $fetcher;
    private ProductParser $parser;
    private array $urls = [];
    private array $visited = [];

    public function __construct(string $urlsFile, string $method)
    {
        $this->db = new Database();
// src/Logger.php
        $this->urlService = new UrlService();
        $this->fetcher = new Fetcher($method);
        $this->parser = new ProductParser();
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
            try {
                //normalize url to standards
                $normalized = $this->urlService->normalizeUrl($url);
                $this->info("normalized the url to $normalized");
            } catch (InvalidArgumentException $e) {
                $this->error("url normalize error:\n {$e->getMessage()}");
                continue;
            }

            //check for duplicates

            $hash = sha1($normalized);
            if (in_array($hash, $this->visited)) {
                echo "already visited ..skipping \n";
                $this->warning("already visited this url, will skip");
                continue;
            }

            //skip if url is not valid
            if (!$this->urlService->isValidUrl($normalized)) {
                $this->error("url failed urlHandle Validation");
                continue;
            }
            $this->info("validated url");
// src/Logger.php

            //fetch
            $html = $this->fetcher->fetch($normalized);
            $this->info("fetched content");

            $this->visited[] = $hash;

            if ($html === false) {
                $this->error("failed to fetch : {$normalized}");
                echo "failed to fetch \n";
                continue;
            }
            //parse
            try {
                //parse the product of the page.
                $product = $this->parser->parse($html);
            } catch (RuntimeException $e) {
                $this->error("parse error for {$normalized}: {$e->getMessage()}");
                echo "failed to parse \n";
                continue;
            }
            echo "parsed successfuly \n";
            $this->info("parsed successfuly");
            //save

            // log missing fields
            $missing = [];
            if ($product['title'] === null) $missing[] = 'title';
            if ($product['price'] === null) $missing[] = 'price';
            if ($product['availability'] === null) $missing[] = 'availability';
            if (!empty($missing)) {
                $this->warning("Missing fields for {$normalized}: " . implode(',', $missing));
            }

            try {
                //insert product to the database
                $this->db->insertProduct($product['title'], $product['price'], $product['availability']);
            } catch (PDOException $e) {
                $this->error("DB insert failed for {$normalized}: " . $e->getMessage());
            }
        }
        echo "Done. Check data/products.sqlite and data/log.txt\n";
    }
}
