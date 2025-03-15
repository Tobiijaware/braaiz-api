<?php

namespace App\Services\Classes;

use App\Models\CurrencyRate;
use App\DTOs\ApiResponseDTO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\Interfaces\IExchangeRateService;


class ExchangeRateService implements IExchangeRateService
{
    public function getAllRates(): ApiResponseDTO
    {
        try {
            $rates = CurrencyRate::all();
            return new ApiResponseDTO(true, $rates, 'Exchange rates retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rates', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to retrieve exchange rates', null, 500);
        }
    }

    public function createRate(array $data): ApiResponseDTO
    {

        $validated = Validator::make($data, [
            'from_currency' => 'required|string|max:3',
            'to_currency' => 'required|string|max:3',
            'rate' => 'required|numeric|min:0'
        ]);

        if ($validated->fails()) {
            return new ApiResponseDTO(false, 'Validation failed', $validated->errors(), 422);
        }

        try 
        {

            //handle duplicates
            $fromCurrency = strtoupper($data['from_currency']);
            $toCurrency = strtoupper($data['to_currency']);
    
            // Check if rate already exists
            $existingRate = CurrencyRate::where('from_currency', $fromCurrency)
                ->where('to_currency', $toCurrency)
                ->first();
    
            if ($existingRate) {
                return new ApiResponseDTO(false, 'Exchange rate already exists', null, 409);
            }

            $rate = CurrencyRate::create([
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'rate' => $data['rate']
            ]);
            return new ApiResponseDTO(true, $rate, 'Exchange rate created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating exchange rate', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to create exchange rate', null, 500);
        }
    }

    public function updateRate(int $id, array $data): ApiResponseDTO
    {

        $validated = Validator::make($data, [
            'rate' => 'required|numeric|min:0'
        ]);

        if ($validated->fails()) {
            return new ApiResponseDTO(false, 'Validation failed', $validated->errors(), 422);
        }

        try {
            $rate = CurrencyRate::find($id);
            if (!$rate) {
                return new ApiResponseDTO(false, 'Exchange rate not found', null, 404);
            }

            $rate->update([
                'rate' => $data['rate']
            ]);

            return new ApiResponseDTO(true, $rate, 'Exchange rate updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating exchange rate', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to update exchange rate', null, 500);
        }
    }

    public function deleteRate(int $id): ApiResponseDTO
    {
        try {
            $rate = CurrencyRate::find($id);
            if (!$rate) {
                return new ApiResponseDTO(false, 'Exchange rate not found', null, 404);
            }

            $rate->delete();
            return new ApiResponseDTO(true, null, 'Exchange rate deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting exchange rate', ['error' => $e->getMessage()]);
            return new ApiResponseDTO(false, 'Failed to delete exchange rate', null, 500);
        }
    }
}
