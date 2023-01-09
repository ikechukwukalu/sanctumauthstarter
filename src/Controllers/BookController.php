<?php

namespace Ikechukwukalu\Sanctumauthstarter\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Ikechukwukalu\Sanctumauthstarter\Models\Book;

class BookController extends Controller
{

    /**
     * Create Book.
     *
     * @header Authorization Bearer {Your key}
     *
     * @bodyParam name string required Example: Once upon a time
     * @bodyParam isbn string required Example: 978-3-16-148410-0
     * @bodyParam authors string[] required Example: ['John Doe', 'Jane Doe']
     * @bodyParam country string required Example: Nigeria
     * @bodyParam number_of_pages int required Example: 1090
     * @bodyParam publisher string required Example: Walt Disney
     * @bodyParam release_date string required Example: 2022-01-01
     *
     * @response 200 {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *      "message": string
     *      "access_token": string
     *  }
     * }
     *
     * @authenticated
     * @group Sample APIs
     * @subgroupDescription This is a Book Management API for
     * testing the <b>require.pin</b> middleware
     */
    public function createBook(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:6|max:100',
            'isbn' => 'required|min:6|max:100|unique:books',
            'authors' => 'required|min:6|max:1000',
            'country' => 'required|max:100',
            'number_of_pages' => 'required|digits_between:1,5',
            'publisher' => 'required|min:6|max:100',
            'release_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $data = (array) $validator->errors()->all();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        if ($book = Book::create((array) $validator->validated())) {
            $data = $book;
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be created'];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }

    /**
     * Fetch books.
     *
     * @header Authorization Bearer {Your key}
     *
     * @urlParam id string The ID of the book Example: 1
     *
     * @response 200 {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *        [
     *          'name': string,
     *          'isbn': string,
     *          'authors': array,
     *          'number_of_pages': int,
     *          'publisher': string,
     *          'country': string,
     *          'release_date': date,
     *        ],
     *        [
     *          'name': string,
     *          'isbn': string,
     *          'authors': array,
     *          'number_of_pages': int,
     *          'publisher': string,
     *          'country': string,
     *          'release_date': date,
     *        ]
     *  }
     * }
     *
     * @authenticated
     * @group Sample APIs
     * @subgroupDescription This is a Book Management API for
     * testing the <b>require.pin</b> middleware
     */
    public function listBooks(Request $request, $id = null): JsonResponse
    {
        if (isset($id)) {
            $data = Book::find($id);
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = Book::paginate(10);
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    /**
     * Update book.
     *
     * @header Authorization Bearer {Your key}
     *
     * @urlParam id string required The ID of the book Example: 1
     * @bodyParam id string required This ID is gotten from the URL param Example: Once upon a time
     * @bodyParam name string Example: Once upon a time
     * @bodyParam isbn string Example: 978-3-16-148410-0
     * @bodyParam authors string[] Example: ['John Doe', 'Jane Doe']
     * @bodyParam country string Example: Nigeria
     * @bodyParam number_of_pages int Example: 1090
     * @bodyParam publisher string Example: Walt Disney
     * @bodyParam release_date string Example: 2022-01-01
     *
     * @response 200
     *
     * //if status_code === 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *          'name': string,
     *          'isbn': string,
     *          'authors': array,
     *          'number_of_pages': int,
     *          'publisher': string,
     *          'country': string,
     *          'release_date': date
     *       }
     * }
     *
     * //if status_code === 500
     *
     * {
     * "status": "fail",
     * "status_code": 500,
     * "data": {
     *      "message": string
     *  }
     * }
     *
     * @authenticated
     * @group Sample APIs
     * @subgroupDescription This is a Book Management API for
     * testing the <b>require.pin</b> middleware
     */
    public function updateBook(Request $request, $id): JsonResponse
    {
        $request->merge(['id' => $id]);

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:books',
            'name' => 'min:6|max:100',
            'isbn' => ['min:6', 'max:100',
                Rule::unique('books')->where(function ($query) use($request) {
                    return $query->where('id', '<>', $request->id);
                })
            ],
            'authors' => 'min:6|max:1000',
            'country' => 'max:100',
            'number_of_pages' => 'digits_between:1,5',
            'publisher' => 'min:6|max:100',
            'release_date' => 'date',
        ]);

        if ($validator->fails()) {
            $data = (array) $validator->errors()->all();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        if (Book::where('id', $request->id)
                ->update((array) $validator
                        ->safe()
                        ->except('id'))
        ) {
            $data = Book::find($request->id);
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be updated'];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }

    /**
     * Delete book.
     *
     * @header Authorization Bearer {Your key}
     *
     * @urlParam id string required The ID of the book Example: 1
     *
     * @response 200
     *
     * //if status_code === 200
     *
     * {
     * "status": "success",
     * "status_code": 200,
     * "data": {
     *          'name': string,
     *          'isbn': string,
     *          'authors': array,
     *          'number_of_pages': int,
     *          'publisher': string,
     *          'country': string,
     *          'release_date': date
     *       }
     * }
     *
     * //if status_code === 500
     *
     * {
     * "status": "fail",
     * "status_code": 500,
     * "data": {
     *      "message": string
     *  }
     * }
     *
     * @authenticated
     * @group Sample APIs
     * @subgroupDescription This is a Book Management API for
     * testing the <b>require.pin</b> middleware
     */
    public function deleteBook(Request $request, $id): JsonResponse
    {
        if (Book::where('id', $id)->delete()
        ) {
            $data = Book::withTrashed()->find($id);
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be deleted'];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }
}
