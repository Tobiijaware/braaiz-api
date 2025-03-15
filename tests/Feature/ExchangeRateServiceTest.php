<?php

namespace Tests\Feature;

use Tests\TestCase;
use Mockery;
use App\Services\Interfaces\IExchangeRateService;
use App\DTOs\ApiResponseDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_create_exchange_rate()
    {
        // Create a mock of the IExchangeRateService interface
        $mockService = Mockery::mock(IExchangeRateService::class);
        
        // Define expected response
        $mockResponse = new ApiResponseDTO(true, null, 'Exchange rate created successfully');

        // Mock the method behavior
        $mockService->shouldReceive('createRate')
            ->once() // Ensure it's called exactly once
            ->with([
                'from_currency' => 'USD',
                'to_currency' => 'EUR',
                'rate' => 0.95,
            ])
            ->andReturn($mockResponse);

        // Inject the mocked service into the application container
        $this->app->instance(IExchangeRateService::class, $mockService);

        // Resolve the service from the container (simulating actual use)
        $service = app(IExchangeRateService::class);
        $response = $service->createRate([
            'from_currency' => 'USD',
            'to_currency' => 'EUR',
            'rate' => 0.95,
        ]);

        // Assertions
        $this->assertTrue($response->success);
        $this->assertEquals('Exchange rate created successfully', $response->message);
    }

    protected function tearDown(): void
    {
        Mockery::close(); // Close mockery to clean up mocks
        parent::tearDown();
    }
}

