<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class BookService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the books service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the books service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = env('BOOKS_SERVICE_BASE_URL');
        $this->secret = env('BOOKS_SERVICE_SECRET');
        
        // Validate configuration
        if (empty($this->baseUri)) {
            throw new \RuntimeException('BOOKS_SERVICE_BASE_URL is not configured in .env file');
        }
    }

    /**
     * Get a single book from the books service
     * @return array
     */
    public function obtainBook($book)
    {
        return $this->performRequest('GET', "/books/{$book}");
    }
}