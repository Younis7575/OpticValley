<?php

namespace App\Support;

/**
 * Minimal allow-list HTML sanitiser for admin-authored rich text
 * (pages, blog posts, product descriptions). Strips scripts, event
 * handlers, style attributes and javascript: URLs.
 */
class Html
{
    private const TAGS = '<p><br><strong><b><em><i><u><a><ul><ol><li><h2><h3><h4><blockquote><img><table><thead><tbody><tr><th><td><hr><span>';

    public static function clean(?string $html): string
    {
        if (! $html) {
            return '';
        }
        $html = preg_replace('#<(script|style|iframe|object|embed|form)[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, self::TAGS);

        $doc = new \DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8"?><div id="__root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath = new \DOMXPath($doc);
        foreach ($xpath->query('//@*') as $attr) {
            $name = strtolower($attr->nodeName);
            $el = $attr->ownerElement;
            $allowed = ['href', 'src', 'alt', 'title', 'target', 'rel', 'colspan', 'rowspan', 'id'];
            if (! in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->nodeName);

                continue;
            }
            if (in_array($name, ['href', 'src'], true) && ! preg_match('#^(https?:|/|\#|mailto:|tel:)#i', trim($attr->value))) {
                $el->removeAttribute($attr->nodeName);
            }
        }
        foreach ($xpath->query('//a[@target="_blank"]') as $a) {
            $a->setAttribute('rel', 'noopener');
        }
        $root = $doc->getElementById('__root');
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }
}
