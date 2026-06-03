<?php

declare(strict_types=1);

namespace App\Product;

use App\Services\Parser;

class ProductParser extends Parser
{

    public function parse(string $html): array
    {
        //get the simple_html_dom from the html
        $dom = $this->getHtml($html);

        if ($dom === false) {
            throw new \RuntimeException('Failed to parse HTML');
        }

        $title = $this->parseElement($dom, ['h1.title']);
        $price = $this->parseElement($dom, ['.current-price']);

        //if price does not contain symbol then search for it and parse it
        if (!preg_match('/[\$€£¥₹₽₩₪₦₱₡₲₴₵]/', $price)) {
            $currency = $this->parseElement($dom, ['.currency-symbol']);
            $price = $currency . $price;
        }

        $availability = $this->parseElement($dom, ['.availability']);

        $dom->clear();
        unset($dom);

        return ['title' => $title, 'price' => $price, 'availability' => $availability];
    }
}
