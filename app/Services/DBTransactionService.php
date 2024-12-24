<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DBTransactionService
{
    public function execute(callable $dbActions): mixed
    {
        try {
            DB::beginTransaction();
            $result = $dbActions() ?? true;
            DB::commit();

            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
