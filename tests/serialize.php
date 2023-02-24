<?php

$array = [
    'Julien' => 'dev1',
    'geotre' => 'dev2'
];

echo serialize($array);

$content = [];

$content[] = 'pe:test';

$imp = implode(':', $content);

echo $imp[1];