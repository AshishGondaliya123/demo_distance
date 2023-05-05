<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function getDistance(Request $request)
    {
        $origin = $request->origin_latitude . "," . $request->origin_longitude;
        $destination = $request->destination_latitude . "," . $request->destination_longitude;

        $ch =  curl_init('https://maps.googleapis.com/maps/api/distancematrix/json?&origins=' . $origin . '&destinations=' . $destination . '&mode=driving&key=AIzaSyAKSwOvQw4ngnepwqk1R8ZxQxHsp37xrXI');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $distance_data = curl_exec($ch);

        $distance_arr = json_decode($distance_data);
        // print_r($distance_arr); exit;

        $duration = "0";
        $distance = "0";
        $status = "";

        if ($distance_arr->status == 'OK') {
            if ($distance_arr->status == 'OK') {
                $destination_addresses = $distance_arr->destination_addresses[0];
                $origin_addresses = $distance_arr->origin_addresses[0];
            } else {
                $status = "The request was Invalid";
                exit();
            }
            if ($origin_addresses == "" or $destination_addresses == "") {
                $status = "Destination or origin address not found";
                exit();
            }
            // Get the elements as array
            $elements = $distance_arr->rows[0]->elements;

            // print_r($elements[0]);
            if ($elements[0]->status == "OK") {
                $distance = $elements[0]->distance->text;
                $duration = $elements[0]->duration->text;
                $status = "OK";
            } else if ($elements[0]->status == "ZERO_RESULTS") {
                $duration = "0";
                $distance = "0";
                $status = "ZERO_RESULTS";
            }
        } else {
            $status = "ZERO_RESULTS";
        }
        
        $data = array('status' => $status, 'duration' => $duration, 'distance' => $distance);
        
        return response()->json($data, 200);
    }

    
}
