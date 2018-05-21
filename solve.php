<?php

require_once 'Utils.php';

Utils::loadCitiesFromFiles("cities.txt");
Utils::setCitiesRelation();

$path = Utils::findShortestPath();
foreach($path as $cityName => $distance) {
    echo "$cityName\n";
}


