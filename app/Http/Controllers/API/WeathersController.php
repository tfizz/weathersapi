<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Weather;
use Illuminate\Support\Facades\Validator;

class WeathersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $lat = $request->lat;
        $lon = $request->lon;
        $start = $request->start;
        $end = $request->end ? $request->end : date('Y-m-d');

        if($start){
            $data = Weather::whereBetween("date",[$start,$end])->get();
        }
        else if($lat && $lon){
            $data = [];
            $result = Weather::all();

            foreach ($result as $record) {
               $record = json_decode($record);
                if($record->location->lat == $lat && $record->location->lon == $lon ){
                   array_push($data,$record);
                }
            }
        }
        else if($lat){
            $data = [];
            $result = Weather::all();

            foreach ($result as $record) {
               $record = json_decode($record);
                if($record->location->lat == $lat){
                   array_push($data,$record);
                }
            }
        }
        else if($lon){
            $data = [];
            $result = Weather::all();

            foreach ($result as $record) {
               $record = json_decode($record);
                if($record->location->lon == $lon){
                   array_push($data,$record);
                }
            }
        }
        else{
            $data = Weather::all();
            
        }

        if(count($data) == 0){
            $message["error"] = "No records found";
            return response()->json($message,404); 
        }

        return response()->json($data); 
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
        $validation = Validator::make($request->all(),[
            'id' => 'required|numeric',
            'date' => 'required|date',
            'location' => 'required',
            'temperature' => 'required|array|max:24',
        ]);

        if($validation->fails()){
            $errors = $validation->errors();
            $message["error"] = $errors;
            return response()->json($message,400);
        }

        // check if request with id exists
        $weather = Weather::where('id', $request->id)->first();

        if($weather){
            $message["error"] = "Same ID exists";
            return response()->json($message,400);
        }

        // save request
        $weather = new Weather;
        $weather->id = $request->id;
        $weather->date = $request->date;
        $weather->location = $request->location;
        $weather->temperature = $request->temperature;
        $weather->save();

        $message["message"] = "Weather information added";
        $message["data"] = $weather;
        return response()->json($message,201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // validate request
        $validation = Validator::make($request->all(),[
            'id' => 'required|numeric',
            'date' => 'required|date',
            'location' => 'required',
            'temperature' => 'required|array|max:24',
        ]);

        if($validation->fails()){
            $errors = $validation->errors();
            $message["error"] = $errors;
            return response()->json($message,400);
        }

        // check if request with id exists
        $weather = Weather::where('id', $request->id)->first();

        $weather->date = $request->date;
        $weather->location = $request->location;
        $weather->temperature = $request->temperature;
        $weather->save();

        $message["message"] = "Weather information updated";
        $message["data"] = $weather;
        return response()->json($message,201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // get options
        $options = $request->all();

        if(!($options)){
            // delete all information
            Weather::truncate();
            $message["message"] = "All weather information deleted";
            return response()->json($message,200);
        }

        $start = $options["start"];
        $end = $options["end"] ? $options["end"] : date("Y-m-d");
        $lat = $options["lat"] ? $options["lat"] : "";
        $lon = $options["lon"] ? $options["lon"] : "";

        Weather::whereBetween("date",[$start,$end])->delete();
        $message["message"] = "Information deleted";

        if($lat || $lon){
            $data = Weather::all();
            $ids = [];
            foreach($data as $record){
                $record = json_decode($record);
                if($lat && $lon && $record->location->lat == $lat && $record->location->lon == $lon){
                    array_push($ids,$record->id);
                }
                else if($lat && $record->location->lat == $lat){
                    array_push($ids,$record->id);
                }
                else if($lon && $record->location->lon == $lon){
                    array_push($ids,$record->id);
                }
            }

            if(count($ids) > 0){
                Weather::destroy($ids);
            }
        }
        return response()->json($message);
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function temperature(Request $request)
    {
        //
        $start = $request->start;
        $end = $request->end ? $request->end : date("Y-m-d");

        if($start){
            $result = Weather::whereBetween("date",[$start,$end])->get();

            if(count($result) == 0){
                $res["lat"] = "";
                $res["lon"] = "";
                $res["city"] = "";
                $res["state"] = "";
                $res["lowest"] = "";
                $res["highest"] = "";
                $res["message"]= "There is no weather data in the given date range";
                return response()->json($res,200); 
            }

            $data = [];

            foreach ($result as $record) {
                $record = json_decode($record);
                $res["lat"] = $record->location->lat;
                $res["lon"] = $record->location->lon;
                $res["city"] = $record->location->city;
                $res["state"] = $record->location->state;
                $res["lowest"] = min($record->temperature);
                $res["highest"] = max($record->temperature);
                array_push($data,$res);
            }

            usort($data,function($a,$b){
                return strcasecmp($a["city"],$b["city"]);
            });

            return response()->json($data,200); 
        }
        else{
            $message["error"] = "Start Date Required";
            return response()->json($message,400); 
        }
        
        
    }

        /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function locations(Request $request)
    {
        //
        $date = $request->start;
        $lat = $request->lat;
        $lon = $request->lon;

        // get all weather results
        $result = Weather::all();

        // variable to store preferred locations
        $preferred_locations = [];

        // variable to store given location information
        $given_location = [];

        // get given location information from coordinates
        foreach($result as $record){
            $record = json_decode($record);
            if($record->location->lat == $lat && $record->location->lon == $lon && $record->date == $date){
                $given_location = $record; // hold information of given location on given date
                break;
            }
        }

        // find preferred locations
        if($given_location){
            foreach ($result as $record) {
                $record = json_decode($record);

                // condition 1
                // if the state of the given information is the same as the current location, skip
                if($given_location->location->state == $record->location->state){
                    continue;
                }


                $data["lat"] = $record->location->lat;
                $data["lon"] = $record->location->lon;
                $data["city"] = $record->location->city;
                $data["state"] = $record->location->state;
                $data["distance"] = $this->calculateDistance([$record->location->lat,$record->location->lon],[$given_location->location->lat,$given_location->location->lon]);
                $data["median_temperature"] = "";

                array_push($preferred_locations,$data);
            }
        }

        return response()->json($preferred_locations);
    }

    /**
     * This function calculates the distance between the current location and the given location
     * @param { Array } currentLocation 1-D array containig latitude and longitude values
     * @param { Array } givenLocation 1-D array containig latitude and longitude values
     * @return { float } distance floating point number of the distance
     */
    private function calculateDistance($currentLocation,$givenLocation){
        $p = 0.017453292519943295;
        $r = 6371.0;
        $lat_diff = ($givenLocation[0] - $currentLocation[0])/2.0;
        $lon_diff = ($givenLocation[1] - $currentLocation[1])/2.0;
        $a = (sin($p * $lat_diff) * sin($p * $lat_diff)) + (sin($p * $lon_diff) * sin($p * $lon_diff)) * cos($p * $currentLocation[0]) * cos($p * $currentLocation[0]);
        $d = 2.0 * $r * asin(sqrt($a));
        return $d;
    }
}
