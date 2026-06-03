<?php

declare(strict_types=1);

require __DIR__ . '/simple_html_dom.php';
require __DIR__ . '/vendor/autoload.php';

use App\Crawler;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


$app = new Crawler();
$app->run();
