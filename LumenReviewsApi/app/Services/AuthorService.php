<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class AuthorService
{
    use ConsumesExternalService;

    public $baseUri;
    public $secret;

    public function __construct()
    {
        $this->baseUri = env('AUTHORS_SERVICE_BASE_URL');
        $this->secret = env('AUTHORS_SERVICE_SECRET');
        
        if (empty($this->baseUri)) {
            throw new \RuntimeException('AUTHORS_SERVICE_BASE_URL is not configured in .env file');
        }
    }

    /**
     * Get a single author from the authors service
     * @return array
     */
    public function obtainAuthor($author)
    {
        return $this->performRequest('GET', "/authors/{$author}");
    }
}
