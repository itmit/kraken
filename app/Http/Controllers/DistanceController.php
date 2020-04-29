<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInfo;
use App\Models\MasterInfo;
use App\Models\Inquiry;
use App\Models\InquiryDetail;
use App\Models\InquiryFile;
use App\Models\TypeOfWork;
use App\Models\MasterToInquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DistanceController extends Controller
{
    public function getTime($from, $to, $mode, $time = 600, $radius = 0)
    {
        return $radius;
        if($radius == 0)
        {
            try {
                $baseURL = "http://dev.virtualearth.net/REST/v1/Routes";  
    
                $key = 'AoQ1_RhiXbz8RQ36RbFTnPkRLu6yNFAfLaKKp-_kK6mrk_fm0yEA3pd-bEltlGl1';  
    
                $wayPoint0 = str_ireplace(" ","%20",$from);  
                $wayPoint1 = str_ireplace(" ","%20",$to);  
                $optimize = "time";  
                $routePathOutput = "Points";  
                $distanceUnit = "km";  
                $travelMode = $mode;  
    
                $routesURL =     
                $baseURL."/".$travelMode."?wp.0=".$wayPoint0."&wp.1=".$wayPoint1  
                ."&optimize=".$optimize."&routePathOutput=".$routePathOutput  
                ."&distanceUnit=".$distanceUnit."&output=xml&key=".$key;  
    
                $output = file_get_contents($routesURL);    
                $response = new \SimpleXMLElement($output);  
    
                $TravelDurationTraffic = $response->ResourceSets->ResourceSet->Resources->Route->TravelDurationTraffic[0];  
                if($TravelDurationTraffic <= $time) $res = true;
                else $res = false;
    
                return $res;
            } catch (Exception $e) {
                $res = false;
                return $res;
            }
        }
        else
        {
            try {
                $baseURL = "http://dev.virtualearth.net/REST/v1/Routes";  
    
                $key = 'AoQ1_RhiXbz8RQ36RbFTnPkRLu6yNFAfLaKKp-_kK6mrk_fm0yEA3pd-bEltlGl1';  
    
                $wayPoint0 = str_ireplace(" ","%20",$from);  
                $wayPoint1 = str_ireplace(" ","%20",$to);  
                $optimize = "time";  
                $routePathOutput = "Points";  
                $distanceUnit = "km";  
                $travelMode = $mode;  
    
                $routesURL =     
                $baseURL."/".$travelMode."?wp.0=".$wayPoint0."&wp.1=".$wayPoint1  
                ."&optimize=".$optimize."&routePathOutput=".$routePathOutput  
                ."&distanceUnit=".$distanceUnit."&output=xml&key=".$key;  
    
                $output = file_get_contents($routesURL);    
                $response = new \SimpleXMLElement($output);  
    
                $TravelDistance = $response->ResourceSets->ResourceSet->Resources->Route->TravelDistance[0];  
                if($TravelDistance <= $radius) $res = true;
                else $res = false;
    
                return $res;
            } catch (Exception $e) {
                $res = false;
                return $res;
            }
        }
    }
}
