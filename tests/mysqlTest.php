<?php

namespace tests;

$provider = new \mysqli("45.145.164.37", "plugin", "0ly6!9nP7", "ArkaniaStudios");

$db = $provider->query("SELECT * FROM claims");
$result = $db->fetch_array() ?? false;
if ($result === false){
    var_dump('nop');
    return;
}

var_dump($result);