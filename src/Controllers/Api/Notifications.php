<?php

namespace App\Controllers\Api;

use App\Contracts\Auth;
use App\Contracts\Notifier;
use App\Entities\OfferNotification;

class Notifications
{
    public function index(Notifier $notifier, Auth $auth): array
    {
        return [
            'status'=> 200,
            'message' => 'OK',
            'data' => $notifier->of($auth->user()),
        ];
    }

    public function update(?OfferNotification $target, Notifier $notifier)
    {
        if ($target === null) {
            return error(404);
        }

        $notifier->see($target);

        return [
            'status' => 200,
            'message' => 'OK',
        ];
    }
}