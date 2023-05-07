<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\Auth;
use App\Contracts\ORM;
use App\Entities\Offer;
use App\Entities\Reservation;
use Lemon\Http\Request;
use Lemon\Http\Response;

class Reservations
{
    public function index(Auth $auth)
    {
        $reservations = $auth->user()->reservations;

        return template('reservations.index', reservations: $reservations);
    }

    public function store(Request $request, Auth $auth, ORM $orm)
    {
        $request->validate([
            'offer' => 'id:offer',
        ], template('offers.show'));

        $user = $auth->user();
        $offer = $orm->getORM()->getRepository(Offer::class)->findByPK((int) $request->get('offer'));
        if ($offer->user->id == $user->id) {
            return template('offers.show', message: 'reservation-author-error');
        }

        $book = $offer->book->id;
        if (!empty(array_filter($user->reservations, fn(Reservation $reservation) => $reservation->offer->book->id == $book))) {
            return template('offers.show', message: 'reservation-exists-error');
        }

        $active = empty($offer->reservations);

        $reservation = new Reservation($offer, $user, $active);
    
        $orm->getEntityManager()->persist($reservation);

        return template('offers.show');
    }

    public function destroy($target, Auth $auth, ORM $orm): Response
    {
        $reservation = $orm->getORM()->getRepository(Reservation::class)->findByPK($target);
        $user = $auth->user()->id;
        if ($reservation->author->id !== $user || $reservation->offer->author->id !== $user) {
            return error(403);
        }

        $offer = $reservation->offer;

        $orm->getEntityManager()->delete($reservation);

        @$offer->reservations[0]->active = true; // TODO but something like this it must be

        return redirect('/');
    }

    public function aprove($hash, ORM $orm, Auth $auth)
    {
        $reservation = $orm->getORM()->getRepository(Reservation::class)->findOne([
            'hash' => $hash,
        ]);

        if ($reservation->offer->author->id !== $auth->user()->id) {
            return template('reservations.aprove', success: false);
        }

        $orm->getEntityManager()->delete($reservation->offer);

        return template('reservations.aprove', success: true);
    }
}
