<?php

namespace Modules\Web\Traits;

use Illuminate\Support\Facades\Http;

trait RajaOngkirTrait
{
    protected function rajaOngkirRequest($endpoint, $method = 'GET', $params = [])
    {
        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = 'https://rajaongkir.komerce.id/api/v1/';

        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
        $response = Http::withoutVerifying()->withHeaders([
            'key' => $apiKey,
            'Accept' => 'application/json'
        ]);

        if (strtoupper($method) === 'POST') {
            return $response->asForm()->post($url, $params);
        }

        return $response->get($url, $params);
    }

    public function getRemoteProvinces($id = null)
    {
        $params = $id ? ['id' => $id] : [];
        $res = $this->rajaOngkirRequest('destination/province', 'GET', $params);

        return $res->json()['data'] ?? [];
    }

    public function getRemoteCities($provinceId = null)
    {
        $endpoint = $provinceId ? "destination/city/{$provinceId}" : "city";

        $res = $this->rajaOngkirRequest($endpoint, 'GET');

        return $res->json()['data'] ?? [];
    }

    public function getDistricts($cityId = null)
    {
        $endpoint = $cityId ? "destination/district/{$cityId}" : "district";

        $res = $this->rajaOngkirRequest($endpoint, 'GET');

        return $res->json()['data'] ?? [];
    }

    public function calculateShippingCost($origin, $destination, $weight, $courier)
    {
        $res = $this->rajaOngkirRequest('calculate/district/domestic-cost', 'POST', [
            'origin'        => $origin,
            'destination'   => $destination,
            'weight'        => $weight,
            'courier'       => $courier
        ]);


        return $res->json()['data'] ?? [];
    }
}
