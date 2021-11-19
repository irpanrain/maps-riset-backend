<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class SampleGeoController extends Controller
{
    /**
     * Get latest position
     * @return response geojson
     **/
    public function latestPosition(Request $request)
    {
        $count = $this->getCountData('latest_position');
        // dd($count); //83
        if ($request->limit > 9 && $request->limit < 101) {
            $limit = (int)round((10*$count)/$request->limit); //modify limit as request zoom in/out client
            $data  = DB::select('SELECT *, ST_AsGeoJson(geom) AS geometry FROM latest_position LIMIT '.$limit);
        }

        return $this->geoJson($data);
    }

    /**
     * Get previous position
     * @return response geojson
     **/
    public function previousPosition(Request $request)
    {
        $count = $this->getCountData('previous_position');
        // dd($count); //529
        if ($request->limit > 9 && $request->limit < 101) {
            $limit = (int)round((10*$count)/$request->limit); //modify limit as request zoom in/out client
            $data = DB::select('SELECT *, ST_AsGeoJson(geom) AS geometry, ST_X(geom::geometry) AS lng, ST_Y(geom::geometry) AS lat
                            FROM previous_position LIMIT '.$limit);
        }

        return $this->geoJson($data);
    }

    /**
     * get count data
     * @param $tabelName
     * @return int $count
    */
    private function getCountData($tabelName) {
        return DB::select('SELECT count(*) AS total FROM '.$tabelName)[0]->total;
    }

    /**
     * @param Object $locales
     * Reformating to geojson
     * @return response
    */
    private function geoJson($locales)
    {
        $features = array();
        foreach($locales as $key => $value) {
            $geometry = json_decode($value->geometry);
            $features[] = array(
                    'type' => 'Feature',
                    'geometry' => array('type' => $geometry->type, 'coordinates' => array((float)$geometry->coordinates[0],(float)$geometry->coordinates[1])),
                    'properties' => array('name' => $value->vname, 'id' => $value->id),
                    'value' => rand(1,3),
            );

            /**
             * if needed to test with random data from server
            */
            // for ($i=0; $i < 5; $i++) {
            //     $rangeX = (int) "0.00"+$i - 5;
            //     $rangeY = (int) "-1.00"+$i + 0.1;
            //     $features[] = array(
            //         'type' => 'Feature',
            //         'geometry' => array('type' => $geometry->type, 'coordinates' => array((float)$geometry->coordinates[0] + $rangeX,(float)$geometry->coordinates[1] + $rangeY)),
            //         'properties' => array('name' => $value->vname, 'id' => $value->id),
            //     );
            // }

        };

        $allfeatures = array('type' => 'FeatureCollection', 'features' => $features, 'count' => count($features));

        return json_encode($allfeatures, JSON_PRETTY_PRINT);
    }

    //TODO create merge coordinates from an object geometry when same vname / id
    /**
     * @param Object $locales
     * Reformating to geojson with merge coodinates
     * @return response
    */
    private function geoJsonMerge($locales)
    {
        $features = array();
        foreach($locales as $key => $value) {
            $geometry = json_decode($value->geometry);
            $features[] = array(
                    'type' => 'Feature',
                    'geometry' => array('type' => $geometry->type, 'coordinates' => array((float)$geometry->coordinates[0],(float)$geometry->coordinates[1])),
                    );
        };

        $allfeatures = array('type' => 'FeatureCollection', 'features' => $features);

        return json_encode($allfeatures, JSON_PRETTY_PRINT);
    }
}
