<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ReviewService;
use App\Services\BookService;

class ReviewController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the review service
     * @var ReviewService
     */
    public $reviewService;

    /**
     * The service to consume the book service
     * @var BookService
     */
    public $bookService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReviewService $reviewService, BookService $bookService)
    {
        $this->reviewService = $reviewService;
        $this->bookService = $bookService;
    }

    /**
     * Retrieve and show all the reviews
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return $this->successResponse($this->reviewService->obtainReviews());
    }

    /**
     * Creates an instance of review
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar que el libro existe antes de crear la reseña
        if ($request->has('book_id')) {
            try {
                $this->bookService->obtainBook($request->book_id);
            } catch (\Exception $e) {
                return $this->errorResponse('The book does not exist', Response::HTTP_NOT_FOUND);
            }
        }

        return $this->successResponse(
            $this->reviewService->createReview($request->all()),
            Response::HTTP_CREATED
        );
    }

    /**
     * Obtain and show an instance of review
     * @return Illuminate\Http\Response
     */
    public function show($review)
    {
        return $this->successResponse($this->reviewService->obtainReview($review));
    }

    /**
     * Updated an instance of review
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $review)
    {
        // Validar que el libro existe si se está actualizando
        if ($request->has('book_id')) {
            try {
                $this->bookService->obtainBook($request->book_id);
            } catch (\Exception $e) {
                return $this->errorResponse('The book does not exist', Response::HTTP_NOT_FOUND);
            }
        }

        return $this->successResponse(
            $this->reviewService->editReview($request->all(), $review)
        );
    }

    /**
     * Removes an instance of review
     * @return Illuminate\Http\Response
     */
    public function destroy($review)
    {
        return $this->successResponse($this->reviewService->deleteReview($review));
    }
}