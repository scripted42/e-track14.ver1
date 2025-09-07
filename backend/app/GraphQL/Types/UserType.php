<?php

namespace App\GraphQL\Types;

class UserType
{
    public function roles($root, array $args)
    {
        if (method_exists($root, 'getRoleNames')) {
            return $root->getRoleNames()->toArray();
        }
        return [];
    }
}
