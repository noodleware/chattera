<?php

if (!function_exists('formatSupportText')) {
    function formatSupportText(): array|string|null
    {
        $text = config('chattera.ui.links.support.text');
        $url = config('chattera.ui.links.support.url');

        if (!$url) {
            return strip_tags(str_replace(['[', ']'], '', $text)); // Remove brackets if no URL
        }

        return preg_replace_callback('/\[(.*?)\]/', function ($matches) use ($url) {
            return '<a href="' . e($url) . '" class="nw-underline focus:nw-outline-none" target="_blank">' . e($matches[1]) . '</a>';
        }, e($text));
    }
}

if (!function_exists('formatTermsAndConditionText')) {
    function formatTermsAndConditionText(): array|string|null
    {
        $text = config('chattera.ui.links.terms.text');
        $url = config('chattera.ui.links.terms.url');

        if (!$url) {
            return strip_tags(str_replace(['[', ']'], '', $text)); // Remove brackets if no URL
        }

        return preg_replace_callback('/\[(.*?)\]/', function ($matches) use ($url) {
            return '<a href="' . e($url) . '" class="nw-underline focus:nw-outline-none" target="_blank">' . e($matches[1]) . '</a>';
        }, e($text));
    }
}

if (!function_exists('cosineSimilarity')) {
    function cosineSimilarity(array $u, array $v): float
    {
        $dotProduct = array_sum(array_map(fn ($uVal, $vVal) => $uVal * $vVal, $u, $v));
        $uLength = sqrt(array_sum(array_map(fn ($uVal) => $uVal * $uVal, $u)));
        $vLength = sqrt(array_sum(array_map(fn ($vVal) => $vVal * $vVal, $v)));

        return $dotProduct / ($uLength * $vLength);
    }
}