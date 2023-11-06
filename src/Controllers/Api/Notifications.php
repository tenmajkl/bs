<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Contracts\Auth;
use App\Contracts\Notifier;
use App\Entities\OfferNotification;
use App\Contracts\ORM;

class Notifications
{
    public function index(Notifier $notifier, Auth $auth): array
    {
        return [
            'status' => 200,
            'message' => 'OK',
            'data' => $notifier->of($auth->user()),
        ];
    }

    public function update(?OfferNotification $target, Notifier $notifier)
    {
        if (null === $target) {
            return error(404);
        }

        $notifier->see($target);

        return [
            'status' => 200,
            'message' => 'OK',
        ];
    }

    public function clear(Notifier $notifier, Auth $auth, ORM $orm)
    {
        $orm->getORM()->getRepository(OfferNotification::class)
                      ->delete([
                          'user.id' => $auth->user()->id,
                          'seen' => 1,
                      ]);

        $this->index($notifier, $auth);
    }

    public function readAll(Notifier $notifier, Auth $auth, ORM $orm)
    {
        $orm->getORM()->getRepository(OfferNotification::class)
                      ->update([
                          'seen' => 1,
                      ])->where([
                          'user.id' => $auth->user()->id,
                      ]);

        $this->index($notifier, $auth);
    }
}
