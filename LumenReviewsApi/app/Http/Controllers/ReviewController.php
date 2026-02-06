<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Review;
use App\Services\BookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;




class ReviewController extends Controller
{
    use ApiResponser;

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
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Return the list of reviews
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::all();
        return $this->successResponse($reviews);
    }

    /**
     * Create one new review
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar que el libro existe antes de crear la reseña
        try {
            $this->bookService->obtainBook($request->book_id);
        } catch (\Exception $e) {
            return $this->errorResponse('The book does not exist', Response::HTTP_NOT_FOUND);
        }

        // Validar datos de entrada
        $rules = [
            'comment' => 'required|max:500',
            'rating' => 'required|integer|min:1|max:5',
            'book_id' => 'required|integer|min:1',
        ];

        $this->validate($request, $rules);

        $review = Review::create($request->all());

        return $this->successResponse($review, Response::HTTP_CREATED);
    }

    /**
     * Obtains and show one review
     * @return Illuminate\Http\Response
     */
    public function show($review)
    {
        $review = Review::findOrFail($review);
        return $this->successResponse($review);
    }

    /**
     * Update an existing review
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $review)
    {
        $review = Review::findOrFail($review);

        // Si se actualiza el book_id, validar que el libro existe
        if ($request->has('book_id')) {
            try {
                $this->bookService->obtainBook($request->book_id);
            } catch (\Exception $e) {
                return $this->errorResponse('The book does not exist', Response::HTTP_NOT_FOUND);
            }
        }

        $rules = [
            'comment' => 'max:500',
            'rating' => 'integer|min:1|max:5',
            'book_id' => 'integer|min:1',
        ];

        $this->validate($request, $rules);

        $review->fill($request->all());

        if ($review->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $review->save();

        return $this->successResponse($review);
    }

    /**
     * Remove an existing review
     * @return Illuminate\Http\Response
     */
    public function destroy($review)
    {
        $review = Review::findOrFail($review);
        $review->delete();

        return $this->successResponse($review);
    }
}
