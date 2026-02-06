<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class UserService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the users service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the users service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = env('USERS_SERVICE_BASE_URL');
        $this->secret = env('USERS_SERVICE_SECRET');
        
        // Validate configuration
        if (empty($this->baseUri)) {
            throw new \RuntimeException('USERS_SERVICE_BASE_URL is not configured in .env file');
        }
    }

    /**
     * Get a single user from the users service
     * @param int $userId
     * @return array
     */
    public function obtainUser($userId)
    {
        return $this->performRequest('GET', "/users/{$userId}");
    }

    /**
     * Verify if a user exists
     * @param int $userId
     * @return bool
     */
    public function userExists($userId)
    {
        try {
            $this->obtainUser($userId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}