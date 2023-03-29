<?php

$array = [
    'Julien' => 'dev1',
    'geotre' => 'dev2'
];

echo serialize($array);

$content = [];

$content[] = 'pe:test';

$imp = implode(':', $content);

echo $imp[1] . PHP_EOL;


echo 'Vous n\'avez pas la permission !';

$start = false;

if (!$start)
    echo 'ON';


var_dump(date('H'));