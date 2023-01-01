<?php

namespace App\Controllers\Api;

use App\Contracts\ORM;
use App\Entities\Book;
use App\Entities\Offer;
use Lemon\Contracts\Validation\Validator;
use Lemon\Http\Request;

class Offers
{
    public function all(ORM $orm, Validator $validator, Request $request): array
    {
        
        $ok = $validator->validate($request->query(), [
            'year' => 'number|max:3',
            'subject' => 'number|max:3',
            'sort' => 'number|max:3'
        ]);

        $book = $orm->getORM()->getRepository(Book::class)->findOne([
            'subject' => $request->query('subject'),
            'year' => $request->query('year'),
        ]);

        if ($book === null || !$ok) {
            return response([
                'error' => '400',
                'message' => 'Bad data',
            ])->code(400);
        }

        return $orm->getORM()->getRepository(Offer::class)->findAll([
              'book' => $book->id
        ]);
    }
}