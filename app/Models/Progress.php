<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property string $status
 * @property array<array-key, mixed>|null $completed_lesson_ids
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereCompletedLessonIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Progress whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Progress extends Model
{
    protected $fillable = ['user_id', 'course_id', 'status', 'completed_lesson_ids', 'saved_code', 'started_at', 'completed_at'];

    protected $casts = [
        'completed_lesson_ids' => 'array',
        'saved_code' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
