<?php

namespace Tests\Feature;

use App\Models\AbstractSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAbstractListTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function abstractSubmission(array $overrides = []): AbstractSubmission
    {
        return AbstractSubmission::create(array_merge([
            'title'             => 'A study of something',
            'authors'           => 'A. Author',
            'presenting_author' => 'A. Author',
            'email'             => 'author@example.com',
            'status'            => 'submitted',
        ], $overrides));
    }

    public function test_the_list_is_paginated_at_ten_per_page(): void
    {
        for ($n = 0; $n < 15; $n++) {
            $this->abstractSubmission(['title' => "Abstract {$n}", 'email' => "author{$n}@example.com"]);
        }

        $response = $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index'))
            ->assertOk();

        $this->assertCount(10, $response->viewData('abstracts')->items());
        $this->assertSame(15, $response->viewData('abstracts')->total());
    }

    public function test_the_second_page_shows_the_remainder(): void
    {
        for ($n = 0; $n < 15; $n++) {
            $this->abstractSubmission(['title' => "Abstract {$n}", 'email' => "author{$n}@example.com"]);
        }

        $response = $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index', ['page' => 2]))
            ->assertOk();

        $this->assertCount(5, $response->viewData('abstracts')->items());
    }

    public function test_search_matches_title_presenting_author_or_email(): void
    {
        $match = $this->abstractSubmission(['title' => 'A Rare Case of Something', 'email' => 'alice@example.com']);
        $other = $this->abstractSubmission(['title' => 'A Different Study', 'email' => 'bob@example.com']);

        $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index', ['q' => 'Rare Case']))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($other->title);

        $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index', ['q' => 'alice@example.com']))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($other->title);
    }

    public function test_type_filter_narrows_to_matching_submissions(): void
    {
        $paper   = $this->abstractSubmission(['title' => 'Paper Submission', 'email' => 'paper@example.com', 'pres_type' => 'paper']);
        $eposter = $this->abstractSubmission(['title' => 'Poster Submission', 'email' => 'poster@example.com', 'pres_type' => 'eposter']);

        $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index', ['type' => 'paper']))
            ->assertOk()
            ->assertSee($paper->title)
            ->assertDontSee($eposter->title);
    }

    public function test_a_soft_deleted_abstract_is_hidden_unless_trashed_is_requested(): void
    {
        $abstract = $this->abstractSubmission();
        $abstract->delete();

        $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index'))
            ->assertOk()
            ->assertDontSee($abstract->title);

        $this->actingAs($this->admin(), 'web')
            ->get(route('abstract.index') . '?trashed')
            ->assertOk()
            ->assertSee($abstract->title);
    }

    public function test_a_guest_is_redirected_away_from_the_list(): void
    {
        $this->get(route('abstract.index'))->assertRedirect(route('login'));
    }
}
