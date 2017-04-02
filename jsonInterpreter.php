<?php

$resturuantList = search($term, $location, $limit);
$resturuantCount  = count($resturuantList);

$randomint = rand(0, $resturuantCount);
$resturuant = $resturuantList[$randomint];


$decodedResturaunt = json_decode($resturuant);
$buisnesses = $decodedRes[0];
$buisnessName = $buisnesses->name;
?>