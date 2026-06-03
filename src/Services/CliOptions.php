<?php
// src/Cli/CliOptions.php
declare(strict_types=1);

namespace App\Services;

class CliOptions
{
    public const FETCH_CURL = 'curl';
    public const FETCH_PUPPETEER = 'puppeteer';
    public const FETCH = 'fetch';
    public const URLPATH = 'urlfile';
    private const VALIDFLAGS  = [self::FETCH, self::URLPATH];
    private const VALIDOPTIONS = [self::FETCH => [self::FETCH_CURL, self::FETCH_PUPPETEER]];


    private array $argv;
    private int $argc;
    private $options = [];
    private $errors = [];



    public function __construct(array $argv, int $argc)
    {
        $this->argv = $argv;
        $this->argc = $argc;
        $this->options = getopt("", [self::FETCH . ':', self::URLPATH . ':']); //["fetch:","urlfile:"]

        if ($this->hasFlags()) {
            $this->validateFlagKeys();
        }
    }


    /**
     * return true if the user has put flags in the command
     */
    public function hasFlags(): bool
    {
        return $this->argc > 1;
    }

    /**
     * will validate flag keys if they are "fetch" or/and "urlfile
     */
    private function validateFlagKeys(): void
    {
        foreach ($this->argv as $arg) {
            if (strpos($arg, '--') === 0) {
                $flagName = substr($arg, 2);
                $flagKey = explode('=', $flagName)[0];

                if (!in_array($flagKey, self::VALIDFLAGS)) {
                    $this->errors[] = "Unknown flag --$flagKey";
                } else {
                    //this will validate the values of the flag keys
                    $this->validateFlagValue($flagKey);
                }
            }
        }
    }


    private function validateFlagValue(string $flagKey): void
    {
        $value = $this->get($flagKey);
        if ($flagKey === 'fetch') {
            //fetch
            if (!in_array($value, self::VALIDOPTIONS[$flagKey])) {
                $this->errors[] = "Unknown value $value for key $flagKey";
            }
        } else {
            //urlfile
            if (!is_file($value)) {
                $this->errors[] = "$value is not a file";
            }
        }
    }


    public function get(string $flag, $default = null): ?string
    {
        return isset($this->options[$flag]) ? $this->options[$flag] : $default;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function validated(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
