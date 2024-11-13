<?php

namespace App\Http\Responses;

class SuccessResponse extends BaseResponse
{
    /**
     * Create a success response.
     *
     * @param string $message
     * @param mixed $data
     * @return array
     */
    public static function create(string $status = "success", string $message, mixed $data = null): array
    {
        return parent::create($status, $message, $data);
    }
}
