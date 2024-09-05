<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\Storage;

class TokenBlacklistService implements Storage
{
    protected $table = 'token_blacklist';

    public function add($key, $value = null, $minutes = null): void
    {
        $expiresAt = Carbon::now()->addSeconds($minutes);

        DB::table($this->table)->insert([
            'token' => $key,
            'expires_at' => $expiresAt,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function forever($key, $value): void
    {
        DB::table($this->table)->insert([
            'token' => $key,
            'expires_at' => Carbon::now()->addYears(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public function get($key)
    {
        $record = DB::table($this->table)->where('token', $key)->first();

        if ($record && Carbon::now()->lessThan($record->expires_at)) {
            return $record->token;
        }

        return null;
    }

    public function destroy($key): void
    {
        DB::table($this->table)->where('token', $key)->delete();
    }

    public function flush(): void
    {
        DB::table($this->table)->delete();
    }
}
