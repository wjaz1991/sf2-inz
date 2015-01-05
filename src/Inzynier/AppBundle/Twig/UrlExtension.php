<?php

namespace Inzynier\AppBundle\Twig;

class UrlExtension extends \Twig_Extension {
    public function getFilters() {
        return([
            new \Twig_SimpleFilter('url_convert', [$this, 'urlFilter']),
        ]);
    }
    
    public function getName() {
        return 'url_extension';
    }
    
    private function callback($match)
    {
        // Prepend http:// if no protocol specified
        $completeUrl = $match[1] ? $match[0] : "http://{$match[0]}";

        return '<a href="' . $completeUrl . '">'
            . $match[2] . $match[3] . $match[4] . '</a>';
    }
    
    public function urlFilter($text) {
        $rexProtocol = '(https?://)?';
        $rexDomain   = '((?:[-a-zA-Z0-9]{1,63}\.)+[-a-zA-Z0-9]{2,63}|(?:[0-9]{1,3}\.){3}[0-9]{1,3})';
        $rexPort     = '(:[0-9]{1,5})?';
        $rexPath     = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery    = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';

        return preg_replace_callback("&\\b$rexProtocol$rexDomain$rexPort$rexPath$rexQuery$rexFragment(?=[?.!,;:\"]?(\s|$))&",
            [$this, 'callback'], htmlspecialchars($text));;
    }
}

