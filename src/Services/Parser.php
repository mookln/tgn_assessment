<?php

declare(strict_types=1);

namespace App\Services;

use App\Traits\Logger;
use simple_html_dom;

class Parser
{
    use Logger;

    public function parseElement(simple_html_dom $dom, array $selectors)
    {
        //get the first text that comes from the selectors.
        $element = $this->firstText($dom, $selectors);

        if (isset($element)) {
            return trim(html_entity_decode($element));
        }
        return null;
    }


    private function firstText(simple_html_dom $dom, array $selectors): ?string
    {
        //foreach selector get the first element and get the text if exists
        foreach ($selectors as $sel) {
            $node = $dom->find($sel, 0);
            if (isset($node)) {
                $text = trim($node->plaintext);
                if (strlen($text) > 0) {
                    return $text;
                }
            }
        }
        return null;
    }

    public function getHtml(string $html): simple_html_dom|false
    {
        /**
         * prepare the html file to remove redundant information and size 
         * before passing it to the simple_html_dom
        */
        $html = HtmlCleaner::prepareHtml($html);
        $dom = str_get_html($html);
        if ($dom !== false) {
            return $dom;
        }

        // if it didnt work remove some extra such as null bytes before parsing
        $cleaned = HtmlCleaner::clean($html);

        $dom = str_get_html($cleaned);
        if ($dom !== false) {
            return $dom;
        }
        return false;
    }
}
