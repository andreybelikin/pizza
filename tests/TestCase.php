<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\AuthTrait;
use Tests\Traits\CartTrait;
use Tests\Traits\UserTrait;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;
    use UserTrait;
    use AuthTrait;
    use CartTrait;
}
