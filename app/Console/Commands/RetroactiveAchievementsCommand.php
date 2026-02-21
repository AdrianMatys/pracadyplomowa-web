<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RetroactiveAchievementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievements:scan';

    protected $description = 'Scan users for retroactive achievements';

    public function handle()
    {
        $users = \App\Models\User::with('progress', 'achievements')->get();
        $this->info('Scanning '.$users->count().' users...');

        foreach ($users as $user) {
            $this->scanForUser($user);
        }

        $this->info('Scan complete.');
    }

    private function scanForUser($user)
    {
        $allCompletedLessonsCount = $user->progress
            ->flatMap(fn ($p) => $p->completed_lesson_ids ?? [])
            ->unique()
            ->count();

        if ($allCompletedLessonsCount >= 1 && ! $user->achievements->contains('id', 'first_steps')) {
            $user->achievements()->attach('first_steps');
            $this->info("Awarded 'first_steps' to user {$user->id}");
        }

        if ($allCompletedLessonsCount >= 50 && ! $user->achievements->contains('id', 'code_master')) {
            $user->achievements()->attach('code_master');
            $this->info("Awarded 'code_master' to user {$user->id}");
        }

        foreach ($user->progress as $progress) {
            if ($progress->status === 'completed') {
                $startedAt = $progress->started_at ?? $progress->created_at;
                $completedAt = $progress->completed_at ?? $progress->updated_at;

                if (! $startedAt || ! $completedAt) {
                    continue;
                }

                if ($completedAt->diffInDays($startedAt) <= 7) {
                    if (! $user->achievements->contains('id', 'fast_learner')) {
                        $user->achievements()->attach('fast_learner');
                        $this->info("Awarded 'fast_learner' to user {$user->id}");
                        break;
                    }
                }
            }
        }
    }
}
