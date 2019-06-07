<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PagesController extends Controller
{
    public function hotels(Request $request)
    {
        $inputDateFormat = 'Y-m-d';

        $destinationCode = $request->destination;
        $checkinDate = \DateTime::CreateFromFormat($inputDateFormat, $request->checkinDate);;
        $checkoutDate = \DateTime::CreateFromFormat($inputDateFormat, $request->checkoutDate);
        $roomQuantity = count($request->rooms);
        $rooms = $request->rooms;

        // Return error if room quantity is more or less than limit
        if ($roomQuantity < 1 || $roomQuantity > 5) {
            $response = [
                'success' => false,
                'error' => 'Maximum number of rooms allowed is 5',
            ];

            return $response;
        }

        // Loop each room requested
        $options = [];
        foreach ($rooms as $room) {

            // $adultNumber = $request->rooms['adults'];
            // $children = $request->rooms['children'];


            $fileList = Storage::files('api-files');

            // Get every file contents
            $hotels = [];
            foreach ($fileList as $fileName) {
                $apiCall = json_decode(Storage::get($fileName));

                foreach ($apiCall->hotels as $hotel) {

                    $hotels[] = $hotel;
                }
            }


            $hotelOptions[] = [
                'quantity' => count($hotels), // Número de resultados (hoteles)
                'hotels' => $hotels,
            ];
        }


        $response = [
            'success' => true,
            'payload' => [
                'options' => $hotelOptions,
            ],
            'debug' => [
                'destinationCode' => $destinationCode,
                'checkinDate' => $checkinDate,
                'checkoutDate' => $checkoutDate,
                'roomQuantity' => $roomQuantity,
                // 'adultNumber' => $adultNumber,
                // 'children' => $children,
            ],
        ];

        return $response;
    }
}
