<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $nickname
 * @property string|null $avatar_url
 * @property string|null $bio
 * @property string $theme
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'avatar_url',
        'bio',
        'theme',
        'experience_points',
        'streak',
        'last_lesson_completed_at',
    ];

    protected $casts = [
        'last_lesson_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateStreak(): void
    {
        $lastCompletedAt = $this->last_lesson_completed_at;
        $now = now();

        if (! $lastCompletedAt) {
            $this->streak = 1;
        } else {
            $lastDate = $lastCompletedAt->copy()->startOfDay();
            $yesterdayDate = $now->copy()->subDay()->startOfDay();

            if ($lastDate->equalTo($yesterdayDate)) {
                $this->streak += 1;
            } elseif ($lastDate->lessThan($yesterdayDate)) {
                $this->streak = 1;
            }
        }

        $this->last_lesson_completed_at = $now;
        $this->save();
    }
}
