<?php

namespace App\Http\Responses;

abstract class BaseResponse
{
    protected string $status;
    protected string $message;
    protected mixed $data;

    public function __construct(string $status, string $message, mixed $data = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    /**
     * Create a response.
     *
     * @param string $status
     * @param string $message
     * @param mixed $data
     * @return array
     */
    public static function create(string $status, string $message, mixed $data = null): array
    {
        return (new static($status, $message, $data))->toArray();
    }
}
