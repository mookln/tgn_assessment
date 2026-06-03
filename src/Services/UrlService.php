<?php

declare(strict_types=1);

namespace App\Services;

use App\Traits\Logger;

class UrlService
{
    use Logger;

    //fallback url list incase no file is provided.
    private const array URLS = [
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/yamaha-yamaha-trbx-304-mgr-ilektriko-baso-misty-green_555208/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/yamaha-yamaha-bb-434-ilektriko-baso-black_556389/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/yamaha-yamaha-bb-434-ilektriko-baso-black_556389/#asdflkjasdf',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/epiphone-epiphone-eb-3-ebony-chrome-hardware-hlektriko-mpaso_562667/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/fender-fender-squier-classic-vibe-60-s-jazz-bass-lrl-3ts-ilektriko-baso_563617/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/fender-fender-standard-jazz-bass-mn-aqua-marine-metallic-electric-bass_857319/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/yamaha-yamaha-trbx-204-ii-gbl-ilektriko-baso_828077/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/fender-fender-american-professional-ii-jazz-bass-rw-dk-nit-fretless-electric-bass_843109/',
        'https://www.nakas.gr/en/proionta/mousika-organa/basa/ilektrika-basa/fender-fender-american-professional-ii-p-bass-rw-olympic-white-ilektriko-baso_843772/',
    ];


    public function loadUrls(string $path): array
    {
        if (strlen($path) === 0) {
            $this->warning("path to urlfile is empty. will use hardcoded");
            return self::URLS;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $urls = array_map('trim', $lines);
        if (count($urls) > 0) {
            return $urls;
        } else {
            $this->warning("URL file not found. will use hardcoded");
            return self::URLS;
        }
    }
}
