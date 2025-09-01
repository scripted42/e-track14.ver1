<?php

namespace PHPSTORM_META {
    
    // Help IntelliSense understand the Request::user() method return type
    override(\Illuminate\Http\Request::user(0), map([
        '' => \App\Models\User::class,
    ]));

    // Help IntelliSense understand the auth() helper return type
    override(\auth(0), map([
        '' => \Illuminate\Contracts\Auth\Guard::class,
    ]));

    // Help IntelliSense understand the auth()->user() method return type
    override(\auth()->user(0), map([
        '' => \App\Models\User::class,
    ]));
}