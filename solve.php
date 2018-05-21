<?php

require_once 'Utils.php';

Utils::loadCitiesFromFiles("cities.txt");
Utils::setCitiesRelation();

//print_r(Utils::$cityDistanceMap);


foreach (Utils::$cities as $city) {
    
}


