<?php

namespace App\GraphQL\Queries;

class Me
{
    public function resolve($_, array $args)
    {
        return auth()->user();
    }
}
