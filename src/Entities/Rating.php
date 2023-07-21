<?php

namespace App\Entities;

use Cycle\Annotated\Annotation\{Entity, Column};
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity]
class Rating
{
    #[Column(type: 'primary')]
    public int $id;

    #[BelongsTo(target: User::class)]
    public User $author;

    #[BelongsTo(target: User::class)]
    public User $rated;

    public function __construct(
        #[Column(type: 'int')]
        public int $rating,
    ) {

    }
}
