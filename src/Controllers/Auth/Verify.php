<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Contracts\ORM;
use App\Entities\School;
use App\Entities\User;
use App\Entities\Year;
use DateTime;
use Lemon\Contracts\Http\Session;
use Lemon\Http\Request;
use Lemon\Http\Responses\RedirectResponse;
use Lemon\Templating\Template;

class Verify
{
    public function get($token, $school, Session $session, ORM $orm): RedirectResponse|Template
    {

        /** @var User $user */        
        if (!($user = $orm->getORM()->getRepository(User::class)->findOne(['verify_token' => sha1($token.$school)]))) {
            return redirect('/register');
        }

        if ($user->createdAt->diff(new DateTime("now"))->i > 10)  {
            $orm->getEntityManager()->delete($user);
            return redirect('/');
        }

        $school = $orm->getORM()->getRepository(School::class)->findOne(['id' => $school]);

        if ($user->role != 0) {
            $teachers = $orm->getORM()->getRepository(Year::class)
                                      ->findOne([
                                          'school_id' => $school->id,
                                          'name' => 'teachers',
                                      ]);
            $user->token = null;
            $user->year = $teachers;
            $orm->getEntityManager()->persist($user)->run();
            $session->dontExpire();
            $session->set('email', $user->email);
            return redirect('/');
        }

        $years = $orm->getORM()->getRepository(Year::class)->findAll(['school_id' => $school->id, 'visible' => true]);

        return template('auth.verify', years: $years);
    }

    public function post($token, $school, Session $session, ORM $orm, Request $request): RedirectResponse|Template
    {
        if (!($user = $orm->getORM()->getRepository(User::class)->findOne(['verify_token' => sha1($token.$school)]))) {
            return redirect('/register');
        }

        if ($user->createdAt->diff(new DateTime("now"))->i > 10)  {
            $orm->getEntityManager()->delete($user);
            return redirect('/');
        }

        $request->validate([
            'year' => 'numeric',
        ], redirect('/verify/'.$token));

        $year = $orm->getORM()->getRepository(Year::class)
            ->findOne([
                'id' => $request->get('year'),
            ])
        ;

        if (null === $year) {
            return redirect('/verify/'.$token.'/'.$school);
        }

        $session->dontExpire();
        $user->year = $year;

        $session->set('email', $user->email);

        $orm->getEntityManager()->persist($user)->run();

        return redirect('/');
    }
}
