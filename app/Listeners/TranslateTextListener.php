<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\history;
use App\Events\TranslationEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Queue\ShouldQueue;

class TranslateTextListener
{

    public function handle(TranslationEvent $event)
    {
        // Add your translation logic here
        $translatedText = $this->translateText($event->text);

        // Broadcast the translated text
        broadcast(new TranslationEvent($translatedText))->toOthers();
    }

    public function translateText($text)
    {
        // Validate the request data
        $validator = Validator::make(['q' => $text], [
            'q' => 'required',
        ]);

        if ($validator->fails()) {
            return 'Translation Error: Text is required';
        }

        // Get the API key from the request header or authorization bearer token
        $apiKey = request()->header('apikey'); // Adjust the header name as needed

        if (empty($apiKey)) {
            return 'Translation Error: Please provide your API key in the header or as a bearer token.';
        }

        // Verify the API key against the keys stored in the users' table
        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return 'Translation Error: Invalid API key.';
        }

        // Prepare data for the translation request
        $data = [
            'q' => $text,
            'source' => 'en', // Assuming the source language is English
            'target' => 'fr', // Change it to the desired target language
            'format' => 'text', // Change it to the desired format
        ];

        $json_data = json_encode($data);

        // Make a request to your translation endpoint
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://translator.cheapmailing.com.ng/translate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Cookie: session=edb08a19-057b-46e5-bd9e-00346901cf2e',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // Check if the decoded_response contains the 'translatedText' key
        if (isset($decoded_response['translatedText'])) {
            $translated_text = $decoded_response['translatedText'];
        } else {
            $translated_text = 'Translation not available.';
        }

        // Save the translation history
        $history = new history;
        $history->text = $text;
        $history->source_language = $data['source'];
        $history->destination_language = $data['target'];
        $history->format = $data['format'];
        $history->response = $translated_text;

        $user->history()->save($history);

        return $translated_text;
    }
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    // public function handle(object $event): void
    // {
    //     //
    // }
}
