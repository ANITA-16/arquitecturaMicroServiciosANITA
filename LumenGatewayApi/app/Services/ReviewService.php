<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class ReviewService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the reviews service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the reviews service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.reviews.base_uri');
        $this->secret = config('services.reviews.secret');
        
        // Validate configuration
        if (empty($this->baseUri)) {
            throw new \RuntimeException('REVIEWS_SERVICE_BASE_URL is not configured in .env file');
        }
    }

    /**
     * Get the full list of reviews from the reviews service
     * @return string
     */
    public function obtainReviews()
    {
        return $this->performRequest('GET', '/reviews');
    }

    /**
     * Create an instance of review using the reviews service
     * @return string
     */
    public function createReview($data)
    {
        return $this->performRequest('POST', '/reviews', $data);
    }

    /**
     * Get a single review from the reviews service
     * @return string
     */
    public function obtainReview($review)
    {
        return $this->performRequest('GET', "/reviews/{$review}");
    }

    /**
     * Edit a single review from the reviews service
     * @return string
     */
    public function editReview($data, $review)
    {
        return $this->performRequest('PUT', "/reviews/{$review}", $data);
    }

    /**
     * Remove a single review from the reviews service
     * @return string
     */
    public function deleteReview($review)
    {
        return $this->performRequest('DELETE', "/reviews/{$review}");
    }
}