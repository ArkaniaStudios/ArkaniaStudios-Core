<?php

namespace tests;

$provider = new \mysqli("45.145.164.37", "plugin", "0ly6!9nP7", "ArkaniaStudios");

$db = $provider->query("SELECT permissions FROM ranks WHERE name='Joueur'");
$mysql = $db->fetch_array()[0];

echo $mysql->getPermissions();