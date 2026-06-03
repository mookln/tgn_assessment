<?php

declare(strict_types=1);

namespace App;

use App\Database;
use App\Traits\Logger;
use PDOException;

final class Crawler
{
    use Logger
    ;
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
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
        //normalize url
        //save duplicates
        //parse
        //save
        //exit;
        echo "Done. Check data/products.sqlite and data/log.txt\n";
    }
}
