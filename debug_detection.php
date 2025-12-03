<?php

$imagePath = '/Volumes/ddexterne/jean-mi/.gemini/antigravity/brain/6012c3ce-6159-46ae-945a-166e5fed7655/uploaded_image_2_1764800095530.png';
$detectWidth = 800;

echo "Analyzing image: $imagePath\n";

$cmdDetect = sprintf(
    "magick %s -auto-orient -resize %dx -colorspace gray -blur 0x5 -threshold 95%% -type bilevel " .
        "-define connected-components:verbose=true " .
        "-define connected-components:area-threshold=1000 " .
        "-connected-components 4 /dev/null",
    escapeshellarg($imagePath),
    $detectWidth
);

echo "Command: $cmdDetect\n\n";

exec($cmdDetect . " 2>&1", $outputDetect);

echo "Output:\n";
print_r($outputDetect);
