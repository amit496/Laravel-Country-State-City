<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{

public function importData()
{

    set_time_limit(300);
    $apiKey = '1359662e4emshe686760bc9f8ff4p160d89jsn2272b8e9a9f7';
    $headers = [
        'x-rapidapi-host' => 'country-state-city-search-rest-api.p.rapidapi.com',
        'x-rapidapi-key' => $apiKey,
    ];

    // Step 1: Fetch all countries
    $countriesResponse = Http::withHeaders($headers)->timeout(300)
        ->get('https://country-state-city-search-rest-api.p.rapidapi.com/allcountries');

    if ($countriesResponse->failed()) {
        return response()->json(['error' => 'Failed to fetch countries'], 500);
    }

    $countries = collect($countriesResponse->json());

    // Chunk 5 countries at a time
    $chunks = $countries->chunk(1);

    foreach ($chunks as $chunk) {
        foreach ($chunk as $countryData) {
            $country = Country::firstOrCreate(
                ['code' => $countryData['isoCode']],
                ['name' => $countryData['name']]
            );

            // Step 2: Fetch states for each country
            $statesResponse = Http::withHeaders($headers)->timeout(300)
                ->get("https://country-state-city-search-rest-api.p.rapidapi.com/states-by-countrycode?countrycode={$country->code}");

            if ($statesResponse->failed()) continue;

            $states = $statesResponse->json();

            foreach ($states as $stateData) {
                $state = State::firstOrCreate(
                    ['code' => $stateData['isoCode']],
                    [
                        'name' => $stateData['name'],
                        'country_id' => $country->id
                    ]
                );

                // Step 3: Fetch cities for each state
                $citiesResponse = Http::withHeaders($headers)->timeout(300)
                    ->get("https://country-state-city-search-rest-api.p.rapidapi.com/cities-by-countrycode-and-statecode?countrycode={$country->code}&statecode={$state->code}");

                if ($citiesResponse->failed()) continue;

                $cities = $citiesResponse->json();

                foreach ($cities as $cityData) {
                    City::Create(
                        ['name' => $cityData['name'], 'state_id' => $state->id],
                        [] // extra fields if any
                    );
                }
            }
        }

        // Sleep 2 seconds between chunks to avoid hitting API rate limits
        sleep(2);
    }

    return response()->json(['message' => 'Data imported in chunks of 5 countries.']);
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
