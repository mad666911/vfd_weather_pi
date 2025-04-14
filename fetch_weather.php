<?php
date_default_timezone_set('America/Toronto');

// ===== CONFIG =====
$postalCode = 'J0J1Z0';                       // Your postal code
$outputFile = '/var/www/html/vfd_weather.json';       // Data file to store weather info

// ===== FETCH DATA =====
$today = trim(fetchWeather($postalCode, '%C %t'));
$tomorrow = trim(fetchWeather($postalCode, '%C %t', '2')); // Day 2 = tomorrow

$data = [
    'today' => cleanWeather("Today: $today"),
    'tomorrow' => cleanWeather("Tomorrow: $tomorrow")
];

file_put_contents($outputFile, json_encode($data));

// ===== FUNCTIONS =====
function fetchWeather($postalCode, $format, $day = '') {
    $dayPath = $day ? "_$day" : '';
    $url = "https://wttr.in/{$postalCode}{$dayPath}?format=" . urlencode($format);
    $opts = ['http' => ['method' => 'GET', 'header' => "User-Agent: VFD-Weather-RPi\r\n"]];
    $ctx = stream_context_create($opts);
    return @file_get_contents($url, false, $ctx) ?: 'N/A';
}

function cleanWeather($text) {
    $replacements = ['°' => '', '+' => '', 'Overcast' => 'Cover'];
    return str_replace(array_keys($replacements), array_values($replacements), $text);
}
