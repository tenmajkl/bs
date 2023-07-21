<?php

namespace App\Entities;

use Cycle\Annotated\Annotation\{Entity, Column};
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity]
class School
{
    #[Column(type: 'primary')]
    public int $id;

    #[HasMany(target: Year::class)]
    public array $years = [];

    public function __construct(
        #[Column(type: 'string')]
        public string $name,
        #[Column(type: 'string')]
        public string $email
    ) {

    }
}
