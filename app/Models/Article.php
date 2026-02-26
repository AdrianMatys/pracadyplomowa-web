<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property bool $is_published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $accent
 * @property-read string $author_name
 * @property-read string $category
 * @property-read string $date
 * @property-read string $excerpt
 * @property-read string $read_time
 * @property-read mixed|null $tags_list
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\ArticleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Article whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'user_id', 'is_published', 'type', 'estimated_time'];

    protected $appends = ['excerpt', 'read_time', 'date', 'author_name', 'tags_list', 'category', 'accent'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 100);
    }

    public function getReadTimeAttribute(): string
    {

        if ($this->estimated_time) {
            return $this->estimated_time.' min';
        }

        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);

        return $minutes.' min';
    }

    public function getDateAttribute(): string
    {
        return $this->created_at->format('d.m.Y');
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->user ? ($this->user->profile->nickname ?? $this->user->email) : 'Unknown';
    }

    public function getTagsListAttribute(): mixed
    {
        return $this->tags->pluck('name');
    }

    public function getCategoryAttribute(): string
    {
        return $this->tags->first()?->name ?? 'General';
    }

    public function getAccentAttribute(): string
    {
        $colors = ['#FF5733', '#33FF57', '#3357FF', '#F333FF', '#33FFF3'];

        return $colors[$this->id % count($colors)] ?? '#FFFFFF';
    }
}
