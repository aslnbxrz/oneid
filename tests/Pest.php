<?php

use Aslnbxrz\OneId\OneIdServiceProvider;

beforeEach(function () {
    $this->app->register(OneIdServiceProvider::class);
});

expect()->extend('toBeOneOf', function ($values) {
    return expect(in_array($this->value, $values, true))->toBeTrue();
});
