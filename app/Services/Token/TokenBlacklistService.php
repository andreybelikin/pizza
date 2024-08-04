<?php

namespace App\Services\Token;

use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\Storage;
use Carbon\Carbon;

class TokenBlacklistService implements Storage
{
    protected $table = 'blacklist';

    public function add($key, $value, $minutes = null): void
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
