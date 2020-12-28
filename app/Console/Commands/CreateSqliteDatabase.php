<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CreateSqliteDatabase extends Command
{
    protected $signature = 'app:create_sqlite_database';
    protected $description = 'Create sqlite database';

    public function handle()
    {
        $fn = 'db.sqlite';
        $disk = Storage::disk('local');

        if (!$disk->exists($fn)) {
            $disk->put($fn, '');
        }

        return 0;
    }
}
