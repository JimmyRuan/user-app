<?php

namespace Tests\Unit\Controllers;

use App\Models\GuestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GuestUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_pagination_data_with_specific_keys(): void
    {
        GuestUser::factory()->count(3)->create();

        $response = $this->getJson(route('users.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'date_of_birth',
                        'created_on',
                    ],
                ],
                'links' => [
                    'first',
                    'last',
                    'prev',
                    'next',
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /**
     * Test sorting functionality of the index endpoint.
     */
    #[DataProvider('sortingFieldsProvider')]
    public function test_index_sorts_guest_users(string $sortField, string $order, array $expectedOrder): void
    {
        Carbon::setTestNow(Carbon::create('2024-09-01T14:00:00Z'));

        GuestUser::factory()->create([
            'id' => 1,
            'date_of_birth' => '1990-01-01',
            'created_at' => now()->subDays(2),
        ]);
        GuestUser::factory()->create([
            'id' => 2,
            'date_of_birth' => '1985-05-05',
            'created_at' => now()->subDay(),
        ]);
        GuestUser::factory()->create([
            'id' => 3,
            'date_of_birth' => '2000-12-12',
            'created_at' => now(),
        ]);


        $response = $this->getJson(route('users.index', ['sort' => $sortField, 'order' => $order]));

        // Assert: Verify sorting order in the response
        $response->assertOk();

        foreach ($expectedOrder as $index => $expectedId) {
            $response->assertJsonPath("data.$index.id", $expectedId);
        }

        // Unfreeze time
        Carbon::setTestNow(null);
    }

    /**
     * Data provider for sorting tests.
     *
     * @return array
     */
    public static function sortingFieldsProvider(): array
    {
        return [
            'Sort by created_on (desc)' => [
                'created_on', 'desc', [3, 2, 1],
            ],
            'Sort by created_on (asc)' => [
                'created_on', 'asc', [1, 2, 3],
            ],
            'Sort by date_of_birth (desc)' => [
                'date_of_birth', 'desc', [3, 1, 2],
            ],
            'Sort by date_of_birth (asc)' => [
                'date_of_birth', 'asc', [2, 1, 3],
            ],
            'Sort by id (desc)' => [
                'id', 'desc', [3, 2, 1],
            ],
            'Sort by id (asc)' => [
                'id', 'asc', [1, 2, 3],
            ],
        ];
    }

    /**
     * Test pagination logic for normalized items per page.
     */
    public function test_pagination_normalizes_items_per_page(): void
    {
        GuestUser::factory()->count(150)->create();

        $testCases = [
            ['per_page' => 5, 'expected_per_page' => 5],    // Valid per_page within range
            ['per_page' => 0, 'expected_per_page' => 1],    // per_page less than minimum
            ['per_page' => 200, 'expected_per_page' => 100], // per_page exceeding maximum
            ['per_page' => -10, 'expected_per_page' => 1],  // Negative per_page
            ['per_page' => 'invalid', 'expected_per_page' => 1], // Non-integer per_page
        ];

        foreach ($testCases as $case) {
            $response = $this->getJson(route('users.index', ['per_page' => $case['per_page']]));

            $response->assertOk()
                ->assertJsonPath('meta.per_page', $case['expected_per_page']);
        }
    }

    /**
     * Test the store endpoint.
     */
    public function test_store_creates_a_new_guest_user(): void
    {
        // Prepare test data
        $data = [
            'name' => 'Test User',
            'date_of_birth' => '1990-01-01',
        ];

        // Call the store endpoint
        $response = $this->postJson(route('users.store'), $data);

        // Assert the response
        $response->assertStatus(201)
            ->assertJsonFragment($data);

        // Assert the user was created in the database
        $this->assertDatabaseHas('guest_users', $data);
    }


    public function test_show_returns_a_specific_guest_user(): void
    {
        $createOnTimeStr = '2024-09-01T14:00:00Z';
        Carbon::setTestNow('2024-09-01T14:00:00Z');

        $guestUser = GuestUser::factory()->create([
            'name' => 'Jane Doe',
            'date_of_birth' => '1990-01-01',
        ]);

        $response = $this->getJson(route('users.show', $guestUser));
        Carbon::setTestNow(null);

        $this->assertEquals("http://localhost/users/$guestUser->id", route('users.show', $guestUser));

        $response->assertStatus(200)
            ->assertJson([
                'id' => $guestUser->id,
                'name' => 'Jane Doe',
                'date_of_birth' => '1990-01-01',
                'created_on' => $createOnTimeStr,
            ]);

    }

    public function test_show_returns_not_found_for_nonexistent_user(): void
    {
        $response = $this->getJson("http://localhost/users/11111111");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Resource not found',
            ]);
    }

    public function test_update_updates_an_existing_guest_user(): void
    {
        $guestUser = GuestUser::factory()->create();

        $data = [
            'name' => 'Updated User',
            'date_of_birth' => '1985-05-05',
        ];

        $response = $this->putJson(route('users.update', $guestUser), $data);

        $response->assertStatus(200)
            ->assertJsonFragment($data);

        $response->assertJsonStructure([
            'id',
            'name',
            'date_of_birth',
            'created_on',
        ]);

        $this->assertDatabaseHas('guest_users', $data);
    }

    public function test_update_returns_not_found_for_nonexistent_user(): void
    {
        $response = $this->putJson(route('users.update', ['guestUser' => 999999]), [
            'name' => 'Updated Name',
            'date_of_birth' => '2000-01-01',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Resource not found',
            ]);
    }

    public function test_update_returns_validation_errors(): void
    {
        $guestUser = GuestUser::factory()->create();

        $data = [
            'name' => '', // Invalid: name is required
            'date_of_birth' => 'invalid-date', // Invalid date format
        ];

        $response = $this->putJson(route('users.update', $guestUser), $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'date_of_birth']);
    }

    public function test_destroy_deletes_a_guest_user(): void
    {
        $guestUser = GuestUser::factory()->create();

        $response = $this->deleteJson(route('users.destroy', $guestUser));

        $response->assertStatus(200)
            ->assertJson(['message' => 'User has been deleted successfully']);

        $this->assertDatabaseMissing('guest_users', ['id' => $guestUser->id]);
    }
}
