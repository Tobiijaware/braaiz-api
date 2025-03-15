<?php

namespace App\Dtos;

class ApiResponseDTO
{
    public bool $success;
    public mixed $data;
    public ?string $message;
    public int $statusCode;

    public function __construct(bool $success, mixed $data = null, ?string $message = null, int $statusCode = 200)
    {
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    // Convert the DTO into a JSON response
    public function toResponse()
    {
        return response()->json([
            'success' => $this->success,
            'data'    => $this->data,
            'message' => $this->message
        ], $this->statusCode);
    }
}
