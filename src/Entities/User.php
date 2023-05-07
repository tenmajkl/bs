<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Traits\DateTimes;
use App\Entities\Traits\Dynamic;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Cycle\ORM\Entity\Behavior;

#[Entity]
#[Behavior\CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',
    column: 'updated_at'
)]
class User
{
    use DateTimes, Dynamic;

    #[Column(type: 'primary')]
    public int $id;

    #[HasMany(target: Offer::class)]
    public array $offers = [];

    #[HasMany(target: Reservation::class)]
    public array $reservations = [];

    public function __construct(
        #[Column(type: 'string')]
        public string $email,
        #[Column(type: 'string')]
        public string $password,
        #[BelongsTo(target: Year::class)]
        public Year $year,
    ) {
    }
}
