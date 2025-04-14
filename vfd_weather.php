<?php
date_default_timezone_set('America/Toronto');

// ===== CONFIG =====
$serialPort = '/dev/ttyUSB0';                   // Serial device
$counterFile = '/var/www/html/vfd_msg_index';            // Rotation counter file
$weatherFile = '/var/www/html/vfd_weather.json';         // Weather data source
$customFile = '/var/www/html/custom_messages.txt'; // Multi-line custom messages
$lineLength = 20;                                // Characters per line

// ===== INIT SERIAL =====
$serial = fopen($serialPort, 'w');
if (!$serial) {
    die("Could not open serial port $serialPort\n");
}
stream_set_blocking($serial, false);

// ===== ROTATE MESSAGE INDEX =====
$msgIndex = (int)@file_get_contents($counterFile);
file_put_contents($counterFile, ($msgIndex + 1) % 3);

// ===== LOAD WEATHER DATA =====
$weather = @json_decode(@file_get_contents($weatherFile), true);
$todayLine = $weather['today'] ?? 'Today: Weather N/A';
$tomorrowLine = $weather['tomorrow'] ?? 'Tomorrow: N/A';

// ===== LOAD CUSTOM MESSAGES =====
$customMessages = file_exists($customFile) ? array_filter(array_map('trim', file($customFile))) : ['I love you!'];
$customLine = $customMessages[array_rand($customMessages)];

// ===== FORMAT LINES =====
$now = new DateTime();
$line1 = str_pad(substr($now->format('l g:i A'), 0, $lineLength), $lineLength);

switch ($msgIndex) {
    case 0: $line2 = $todayLine; break;
    case 1: $line2 = $tomorrowLine; break;
    case 2: $line2 = $customLine; break;
}
$line2 = str_pad(substr($line2, 0, $lineLength), $lineLength);

// ===== SEND TO VFD =====
$fullText = $line1 . $line2;
fwrite($serial, $fullText);

// ===== Console Output (Optional) =====
echo "VFD Update:\n$line1\n$line2\n";
