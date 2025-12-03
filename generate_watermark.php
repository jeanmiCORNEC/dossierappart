<?php

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

require __DIR__ . '/vendor/autoload.php';

$manager = new ImageManager(new Driver());

// Create a large transparent canvas
$width = 2000;
$height = 2000;
$img = $manager->create($width, $height);

// Text settings
$text = "POUR LOCATION UNIQUEMENT\n" . date('d/m/Y');
$fontSize = 150;
$angle = 45;

// Draw text
$img->text($text, $width / 2, $height / 2, function ($font) use ($fontSize, $angle) {
    // $font->file(__DIR__ . '/public/fonts/DejaVuSans.ttf'); 
    $font->size($fontSize);
    $font->color('rgba(50, 50, 50, 0.15)'); // Dark gray with 15% opacity
    $font->align('center');
    $font->valign('middle');
    $font->angle($angle);
});

// Save
$path = __DIR__ . '/storage/app/watermark_master.png';
$img->save($path);

echo "Watermark generated at: $path\n";
