<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class PostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['password' => 'portfolio123']);
        $this->otherUser = User::factory()->create(['password' => 'portfolio123']);
    }

    // --- accounts -------------------------------------------------------------

    public function test_an_account_can_be_created_and_is_logged_in(): void
    {
        $this->post('/register', [
            'name' => 'Lore',
            'email' => 'nieuw@voorbeeld.be',
            'password' => 'portfolio123',
            'password_confirmation' => 'portfolio123',
        ])->assertRedirect('/');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'nieuw@voorbeeld.be']);
    }

    public function test_a_password_is_stored_hashed_and_never_as_plain_text(): void
    {
        $this->post('/register', [
            'name' => 'Lore',
            'email' => 'hash@voorbeeld.be',
            'password' => 'portfolio123',
            'password_confirmation' => 'portfolio123',
        ]);

        $stored = User::where('email', 'hash@voorbeeld.be')->firstOrFail()->password;

        $this->assertNotSame('portfolio123', $stored);
        $this->assertStringStartsWith('$2y$', $stored);
    }

    public function test_a_name_longer_than_ten_characters_is_accepted(): void
    {
        $this->post('/register', [
            'name' => 'Bartholomeus van der Berg',
            'email' => 'lang@voorbeeld.be',
            'password' => 'portfolio123',
            'password_confirmation' => 'portfolio123',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'lang@voorbeeld.be']);
    }

    public function test_logging_in_with_the_right_password_works(): void
    {
        $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'portfolio123',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($this->user);
    }

    public function test_a_wrong_password_gives_an_error_message(): void
    {
        $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'verkeerd',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_unknown_email_gives_the_same_error_message(): void
    {
        $this->post('/login', [
            'email' => 'niemand@voorbeeld.be',
            'password' => 'portfolio123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logging_out_works(): void
    {
        $this->actingAs($this->user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    // --- berichten maken ------------------------------------------------------

    public function test_creating_a_post_stores_it_for_the_logged_in_user(): void
    {
        $this->actingAs($this->user)
            ->post('/create-post', [
                'title' => 'Mijn eerste bericht',
                'body' => 'De inhoud van het bericht.',
            ])
            ->assertRedirect('/');

        $this->assertDatabaseHas('posts', [
            'title' => 'Mijn eerste bericht',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_a_user_id_in_the_form_body_is_ignored(): void
    {
        $this->actingAs($this->user)->post('/create-post', [
            'title' => 'Bericht',
            'body' => 'Inhoud',
            'user_id' => $this->otherUser->id,
        ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Bericht',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_a_post_needs_a_title_and_a_body(): void
    {
        $this->actingAs($this->user)
            ->post('/create-post', [])
            ->assertSessionHasErrors(['title', 'body']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_a_guest_cannot_create_a_post(): void
    {
        $this->post('/create-post', ['title' => 'T', 'body' => 'B'])
            ->assertRedirect('/login');

        $this->assertDatabaseCount('posts', 0);
    }

    // --- berichten van anderen ------------------------------------------------

    public function test_a_user_can_edit_their_own_post(): void
    {
        $post = Post::factory()->of($this->user)->create(['title' => 'Origineel']);

        $this->actingAs($this->user)
            ->put("/edit-post/{$post->id}", ['title' => 'Bijgewerkt', 'body' => 'Nieuw'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Bijgewerkt']);
    }

    public function test_a_user_can_delete_their_own_post(): void
    {
        $post = Post::factory()->of($this->user)->create();

        $this->actingAs($this->user)
            ->delete("/delete-post/{$post->id}")
            ->assertRedirect('/');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_a_user_cannot_open_the_edit_form_of_another_users_post(): void
    {
        $post = Post::factory()->of($this->otherUser)->create();

        $this->actingAs($this->user)
            ->get("/edit-post/{$post->id}")
            ->assertForbidden();
    }

    public function test_a_user_cannot_update_another_users_post(): void
    {
        $post = Post::factory()->of($this->otherUser)->create(['title' => 'Van een ander']);

        $this->actingAs($this->user)
            ->put("/edit-post/{$post->id}", ['title' => 'Overgenomen', 'body' => 'Gewijzigd'])
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Van een ander']);
    }

    public function test_a_user_cannot_delete_another_users_post(): void
    {
        $post = Post::factory()->of($this->otherUser)->create();

        $this->actingAs($this->user)
            ->delete("/delete-post/{$post->id}")
            ->assertForbidden();

        $this->assertDatabaseCount('posts', 1);
    }

    // --- homepage -------------------------------------------------------------

    public function test_the_homepage_only_shows_your_own_posts(): void
    {
        $mine = Post::factory()->of($this->user)->create(['title' => 'Mijn bericht']);
        $theirs = Post::factory()->of($this->otherUser)->create(['title' => 'Bericht van een ander']);

        $this->actingAs($this->user)
            ->get('/')
            ->assertOk()
            ->assertSee($mine->title)
            ->assertDontSee($theirs->title);
    }

    public function test_a_guest_sees_the_login_page_without_posts(): void
    {
        Post::factory()->of($this->user)->create(['title' => 'Verborgen']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Verborgen');
    }
}
