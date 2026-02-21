<?php

namespace Tests\Feature\Admin;

use App\Models\Article;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNewsTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        $admin = User::factory()->admin()->create();
        Profile::factory()->create(['user_id' => $admin->id]);

        return $admin;
    }

    private function createArticle(array $attrs = []): Article
    {
        $admin = User::factory()->admin()->create();

        return Article::factory()->create(array_merge([
            'user_id' => $admin->id,
            'type' => 'news',
        ], $attrs));
    }

    // ── Authorization ────────────────────────────────────────────

    public function test_non_admin_cannot_access_admin_news(): void
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/admin/news');

        $response->assertStatus(403);
    }

    // ── List ─────────────────────────────────────────────────────

    public function test_admin_can_list_news(): void
    {
        $admin = $this->createAdmin();
        Article::factory()->count(3)->create(['user_id' => $admin->id, 'type' => 'news']);

        $response = $this->actingAs($admin)->getJson('/api/admin/news');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // ── Create ───────────────────────────────────────────────────

    public function test_admin_can_create_news(): void
    {
        $admin = $this->createAdmin();
        $tag = Tag::factory()->create();

        $response = $this->actingAs($admin)->postJson('/api/admin/news', [
            'title' => 'Breaking News',
            'content' => 'This is some content.',
            'type' => 'news',
            'tags' => [$tag->id],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('articles', ['title' => 'Breaking News']);
    }

    public function test_create_news_requires_title_and_content(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->postJson('/api/admin/news', [
            'type' => 'news',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    // ── Show ─────────────────────────────────────────────────────

    public function test_admin_can_show_article(): void
    {
        $admin = $this->createAdmin();
        $article = $this->createArticle(['title' => 'Show Me']);

        $response = $this->actingAs($admin)->getJson("/api/admin/news/{$article->id}");

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Show Me']);
    }

    // ── Update ───────────────────────────────────────────────────

    public function test_admin_can_update_article(): void
    {
        $admin = $this->createAdmin();
        $article = $this->createArticle();

        $response = $this->actingAs($admin)->putJson("/api/admin/news/{$article->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'type' => 'article',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Title']);
    }

    // ── Delete ───────────────────────────────────────────────────

    public function test_admin_can_delete_article(): void
    {
        $admin = $this->createAdmin();
        $article = $this->createArticle();

        $response = $this->actingAs($admin)->deleteJson("/api/admin/news/{$article->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    // ── Toggle Publish ───────────────────────────────────────────

    public function test_admin_can_toggle_publish(): void
    {
        $admin = $this->createAdmin();
        $article = $this->createArticle(['is_published' => false]);

        $response = $this->actingAs($admin)->patchJson("/api/admin/news/{$article->id}/toggle-publish");

        $response->assertOk()
            ->assertJson(['is_published' => true]);
    }

    public function test_admin_can_unpublish_article(): void
    {
        $admin = $this->createAdmin();
        $article = $this->createArticle(['is_published' => true]);

        $response = $this->actingAs($admin)->patchJson("/api/admin/news/{$article->id}/toggle-publish");

        $response->assertOk()
            ->assertJson(['is_published' => false]);
    }
}
