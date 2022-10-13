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
    public function externalBooks(Request $request): JsonResponse
    {
        $queryString = ['pageSize' => 3];
        foreach(['name', 'page'] as $param) {
            if ($request->has($param)) {
                $queryString[$param] = $request->query($param);
            }
        }

        $response = Http::get('https://www.anapioficeandfire.com/api/books', $queryString);

        $data = [];
        foreach($response->json() as $json) {
            $data[] = [
                'name' => $json['name'],
                'isbn' => $json['isbn'],
                'authors' => $json['authors'],
                'number_of_pages' => $json['numberOfPages'],
                'publisher' => $json['publisher'],
                'country' => $json['country'],
                'release_date' => $json['released'],
            ];
        }

        $numberOfItems = count($data);
        $status = $response->successful() ? $numberOfItems === 0 ? 'Not Found' : 'success' : 'fail';
        $status_code = $numberOfItems === 0 ? 404 : $response->status();

        return $this->httpJsonResponse($status, $status_code, $data);
    }

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
            $data = (array) $validator->messages();
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
        }

        if ($book = Book::create((array) $validator->validated())) {
            $data = $book;
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be created'];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }

    public function listBooks(Request $request): JsonResponse
    {
        if (isset($request->id)) {
            $data = Book::find($request->id);
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = Book::paginate(10);
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
    }

    public function updateBook(Request $request): JsonResponse
    {
        $request->request->add(['id' => $request->id]);

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
            $data = (array) $validator->messages();
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

    public function deleteBook(Request $request): JsonResponse
    {
        if (Book::where('id', $request->id)->delete()
        ) {
            $data = Book::withTrashed()->find($request->id);
            return $this->httpJsonResponse(trans('sanctumauthstarter::general.success'), 200, $data);
        }

        $data = ['message' => 'Book could not be deleted'];
        return $this->httpJsonResponse(trans('sanctumauthstarter::general.fail'), 500, $data);
    }
}
