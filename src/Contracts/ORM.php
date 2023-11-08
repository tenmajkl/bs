<?php

declare(strict_types=1);

namespace App\Contracts;

use Cycle\Database\DatabaseInterface;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;

interface ORM
{
    public function getORM(): ORMInterface;

    public function getEntityManager(): EntityManagerInterface;

    public function db(): DatabaseInterface;
}
