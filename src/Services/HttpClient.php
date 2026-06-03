<?php

declare(strict_types=1);

namespace App\Services;

use App\Traits\Logger;
use CurlHandle;

class HttpClient
{
    use Logger;

    private const REQUEST_TIMEOUT = 10; // seconds
    private const MAX_REDIRECTS = 5;


    private const RATE_LIMIT_DELAY = 1; // seconds
    private $lastRequestTime = 0;

    private array $defaultOpts = [
        //basic options
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
        CURLOPT_TCP_KEEPALIVE => 1,
        CURLOPT_TCP_KEEPIDLE => 120,
        //timeout settings
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
        //headers
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; PHP Scraper/1.0)',
        //ssl verification
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        // Performance
        CURLOPT_ENCODING => 'gzip, deflate',

        // Get headers in response
        CURLOPT_HEADER => false,

        // Disable curl progress meter
        CURLOPT_NOPROGRESS => true,
    ];


    /**
     * configure the cUrl before using
     */
    private function configureCURL(string $url): CurlHandle|false
    {
        $curl = curl_init();
        $this->defaultOpts[CURLOPT_URL] = $url;
        curl_setopt_array($curl, $this->defaultOpts);
        return $curl;
    }


    private function get(string $url): string|false
    {
        $curl = $this->configureCURL($url);
        $this->enforceRateLimit();

        if ($curl === false) {
            return false;
        }

        $response = curl_exec($curl);
        if ($response === false) {
            $this->error('cUrl error (' . $url . '): \n' . curl_error($curl));
            return false;
        }

        //perform some validations
        if ($this->validateResponse($response, curl_getinfo($curl))) {
            return $response;
        }
        return false;
    }

    public function fetch(string $url): string|false
    {
        $attempt = 0;
        do {
            $attempt++;
            $result = $this->get($url);
            if ($result !== false) {
                return $result;
            }
        } while ($attempt <= 1);
        return false;
    }

    /**
     * sleep between requests
     */
    private function enforceRateLimit()
    {
        $elapsed = microtime(true) - $this->lastRequestTime;

        if ($elapsed < self::RATE_LIMIT_DELAY) {
            $sleeptime = (int) ($this->lastRequestTime + self::RATE_LIMIT_DELAY - microtime(true)) * 1000000;
            usleep($sleeptime);
        }

        $this->lastRequestTime = microtime(true);
    }


    /**
     * Validate HTTP response code
     */
    private function validateResponseCode(int $code): bool
    {
        if ($code >= 200 && $code < 300) {
            return true;
        }

        $this->warning("HTTP error: $code");
        return false;
    }

    /**
     * Validate content is HTML
     */
    private function isValidContentType(string $contentType): bool
    {
        if (empty($contentType)) {
            $this->warning("response has empty content type");
            return false;
        }
        if (stripos($contentType, 'text/html') === 0) {
            return true;
        }
        $this->warning("response content type is not text/html");
        return false;
    }

    private function validateResponse(string $response, array $info): bool
    {
        $validatedCode = $this->validateResponseCode($info['http_code']);
        $validatedContentType = $this->isValidContentType($info['content_type'] ?? '');

        return $validatedCode && $validatedContentType;
    }
}
