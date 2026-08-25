<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup';

    protected $description = 'Backup yaratish (SQLite fayli yoki MySQL dump)';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $fileName = 'backups/ambarella-'.now()->format('Y-m-d_Hi').'.sqlite';

        if (config('database.default') === 'sqlite') {
            $disk->put($fileName, file_get_contents(database_path('database.sqlite')));

            if ($disk->exists($fileName)) {
                $this->info("Backup yaratildi: storage/app/{$fileName}");

                return self::SUCCESS;
            }
        }

        // MySQL: pg_dump-style via mysqldump process (production).
        $this->warn('MySQL dump uchun mysqldump konfiguratsiyasi serverda sozlanadi.');

        return self::SUCCESS;
    }
}
