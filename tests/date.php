<?php

/*echo date('H') . PHP_EOL;
echo date('i');


$a = 16;
$b = $a >> 4;
echo $b;*/

$nombre = readline('Entrez une url: ');
if (preg_match('#^https://discord\.com/api/webhooks/+#', $nombre)){
    echo 'Url valide';
}else
    echo 'Url invalide';