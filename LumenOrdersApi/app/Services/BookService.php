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
     * @param int $bookId
     * @return array
     */
    public function obtainBook($bookId)
    {
        return $this->performRequest('GET', "/books/{$bookId}");
    }

    /**
     * Get multiple books from the books service
     * @param array $bookIds
     * @return array
     */
    public function obtainBooks($bookIds = [])
    {
        $query = !empty($bookIds) ? ['ids' => implode(',', $bookIds)] : [];
        return $this->performRequest('GET', '/books', $query);
    }

    /**
     * Verify if a book exists and has stock
     * @param int $bookId
     * @param int $quantity
     * @return array ['exists' => bool, 'hasStock' => bool, 'book' => array]
     */
    public function checkBookAvailability($bookId, $quantity = 1)
    {
        try {
            $book = $this->obtainBook($bookId);
            
            $hasStock = true;
            if (isset($book['stock'])) {
                $hasStock = $book['stock'] >= $quantity;
            }

            return [
                'exists' => true,
                'hasStock' => $hasStock,
                'book' => $book
            ];
        } catch (\Exception $e) {
            return [
                'exists' => false,
                'hasStock' => false,
                'book' => null
            ];
        }
    }

    /**
     * Validate inventory for multiple books
     * @param array $items Array of ['book_id' => id, 'quantity' => qty]
     * @return array ['valid' => bool, 'errors' => array, 'books' => array]
     */
    public function validateInventory($items)
    {
        $errors = [];
        $books = [];
        $valid = true;

        foreach ($items as $item) {
            $bookId = $item['book_id'] ?? null;
            $quantity = $item['quantity'] ?? 1;

            if (!$bookId) {
                $errors[] = "Missing book_id in item";
                $valid = false;
                continue;
            }

            $availability = $this->checkBookAvailability($bookId, $quantity);

            if (!$availability['exists']) {
                $errors[] = "Book with ID {$bookId} does not exist";
                $valid = false;
            } elseif (!$availability['hasStock']) {
                $stock = $availability['book']['stock'] ?? 0;
                $errors[] = "Insufficient stock for book ID {$bookId}. Available: {$stock}, Requested: {$quantity}";
                $valid = false;
            } else {
                $books[$bookId] = $availability['book'];
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors,
            'books' => $books
        ];
    }
}