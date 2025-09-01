<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific
| test case class. By default, that class is "PHPUnit\Framework\TestCase".
|
| For service-based tests, you may want to bind it to a different class
| that extends that one. That is what we are doing here.
|
*/

uses(TestCase::class)->in('Feature');
