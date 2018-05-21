<?php

Class Utils
{

    public static $cities          = [];
    public static $cityDistanceMap = [];

    /**
     * Set city relations
     * @return \PHPAlgorithms\Dijkstra
     */
    public static function setCitiesRelation()
    {        
        foreach (Utils::$cities as $outsideIndex => $cityOutsideLoop) {
            Utils::$cityDistanceMap[$cityOutsideLoop['city_name']] = [];
            foreach (Utils::$cities as $insideIndex => $cityInsideLoop) {
                if ($outsideIndex == $insideIndex) {
                    continue;
                }
                $distance = Utils::vincentyGreatCircleDistance($cityOutsideLoop['lat'], $cityOutsideLoop['long'], $cityInsideLoop['lat'], $cityInsideLoop['long']);
                Utils::$cityDistanceMap[$cityOutsideLoop['city_name']][$cityInsideLoop['city_name']] = $distance;
            }
            //Sort nearest city
            uasort(Utils::$cityDistanceMap[$cityOutsideLoop['city_name']], 'customerSortCompare');
        }
    }
    
    
    /**
     * Find the shortest path base on current data
     * 
     * @return array of point.
     */
    public static function findShortestPath() {
        $firstCity = Utils::$cities[0]['city_name'];
        $path = [$firstCity => 0];
        reset(self::$cityDistanceMap[$firstCity]);
        $nextCity = key(self::$cityDistanceMap[$firstCity]);
        print_r("Next city: $nextCity\n");
        while ($nextCity) {
            $currentCity = $nextCity;
            foreach (self::$cityDistanceMap[$currentCity] as $cityName => $distance) {
                print_r("Loop city name: $cityName\n");
                if (!array_key_exists($cityName, $path)) {
                    $nextCity = $cityName;
                    $path[$cityName]  = $distance;
                }
            }
            
            if ($currentCity == $nextCity) {
                $nextCity = null;
            }
        }
        
        
        return $path;
    }

    /**
     * Load data from file and store data into static variable
     * 
     * @param string $filePath path to file
     * 
     * @throws Exception
     */
    public static function loadCitiesFromFiles($filePath)
    {
        $myfile = fopen($filePath, "r") or die("Unable to open file!");
        //Traverse line by line to get data
        while (!feof($myfile))
        {
            $lineContent     = trim(fgets($myfile));
            $lineParts       = explode(' ', $lineContent);
            $countOfLinePart = count($lineParts);
            if (count($lineParts) < 3)
            {
                throw new Exception("The row is in valid. Please fix this content: $lineContent");
            }
            //Get data form part of line
            $long     = trim($lineParts[$countOfLinePart - 1]);
            $lat      = trim($lineParts[$countOfLinePart - 2]);
            $cityName = trim(str_replace(" " . $lineParts[$countOfLinePart - 2] . " " . $lineParts[$countOfLinePart - 1], "", $lineContent));

            //Store data into global data
            self::$cities[] = array('city_name' => $cityName, 'lat' => $lat, 'long' => $long);
        }
        fclose($myfile);
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     * 
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public static function vincentyGreatCircleDistance(
    $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo   = deg2rad($latitudeTo);
        $lonTo   = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a        = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b        = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }

}


function customerSortCompare($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
}