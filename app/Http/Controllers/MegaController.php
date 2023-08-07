<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\history;
use App\Models\pricing;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MegaController extends Controller
{
    public function apikey(Request $request)
    {
        $user =  User::find(Auth::user()->id);
        $apikey = Str::random(40);
        if ($user) {
            $user->api_key = $apikey;
            $user->update();

            return response()->json([
                'statusCode' => true,
                'message' => 'Apikey has been created successfully',
            ]);
        };
    }

    public function pricing(Request $request)
    {
        $request->validate([
            "name" => "required",
            "amount" => "required",
            "description" => "required",
        ]);

        $price = new pricing;
        $price->name = $request->name;
        $price->amount = $request->amount;
        $price->description = $request->description;
        $price->mode = $request->mode;
        $price->save();

        return response()->json([
            "statusCode" => 200,
            "message" => "Price has been updated successfully",
        ]);
    }

    public function translator(Request $request)
    {
        $data = array(
            "q" => $request->q,
            "source" => $request->source,
            "target" => $request->target,
            "format" => $request->format
        );

        $json_data = json_encode($data);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://62.171.157.189:5000/translate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: session=edb08a19-057b-46e5-bd9e-00346901cf2e'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        // echo $response;
        $decoded_response = json_decode($response, true);
        // echo $json_decode["translatedText"];


        // Check if the decoded_response contains the 'translatedText' key
        if (isset($decoded_response['translatedText'])) {
            $translated_text = $decoded_response['translatedText'];
        } else {
            $translated_text = 'Translation not available.';
        }

        $history = new history;
        $history->user_id = Auth::user()->id;
        $history->text = $request->q;
        $history->source_language = $request->source;
        $history->destination_language = $request->target;
        $history->format = $request->format;
        $history->response = $translated_text;
        $history->save();
        if($history->save()){
            return response()->json([
                "status code" => 200,
                "message" => $translated_text
            ]);
        }else{
            return response()->json([
                "status code" => 422,
                "message" => "error",
            ]);
        }

    }

}
