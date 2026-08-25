<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GreenCoinService;
use Illuminate\Console\Command;

class SyncAchievements extends Command
{
    protected $signature = 'app:sync-achievements';

    protected $description = 'Yutuqlarni avtomatik bajarish (streak, birinchi vazifa va h.k.)';

    public function handle(GreenCoinService $coins): int
    {
        $granted = 0;

        User::where('is_banned', false)->chunkById(200, function ($users) use ($coins, &$granted): void {
            foreach ($users as $user) {
                $codes = collect();

                if ($user->login_streak >= 7) {
                    $codes->push('streak_7');
                }
                if ($user->greencoin_balance >= 100) {
                    $codes->push('coins_100');
                }
                if ($user->applications()->exists()) {
                    $codes->push('first_application');
                }
                if ($user->ecoCompletions()->where('status', 'approved')->exists()) {
                    $codes->push('first_eco');
                }

                foreach ($codes as $code) {
                    $achievement = \App\Models\Achievement::where('code', $code)->first();

                    if (! $achievement || $user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                        continue;
                    }

                    $user->achievements()->attach($achievement->id);
                    $coins->credit($user, $achievement->reward, "Yutuq: {$achievement->title}");
                    $granted++;
                }
            }
        });

        $this->info("Yangi berilgan yutuqlar: {$granted}");

        return self::SUCCESS;
    }
}
