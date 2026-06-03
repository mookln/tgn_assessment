<?php

declare(strict_types=1);

require __DIR__ . '/simple_html_dom.php';
require __DIR__ . '/vendor/autoload.php';

use App\Crawler;
use App\Services\CliOptions;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$cli = new CliOptions($argv, $argc);
$method = CliOptions::FETCH_CURL;
$urlsFile = '';

//if user has put flags in the command
if ($cli->hasFlags()) {
    if ($cli->hasErrors()) {
        //print errors
        foreach ($cli->getErrors() as $error) {
            echo "$error \n";
        }
    } else {
        //set method and file path to user's specific
        $method = $cli->get(CliOptions::FETCH) ? $cli->get(CliOptions::FETCH) : $method;
        $urlsFile = $cli->get(CliOptions::URLPATH) ? $cli->get(CliOptions::URLPATH) : $urlsFile;
    }
}




$app = new Crawler($urlsFile, $method);
$app->run();
