<?php

namespace Tests\Unit\Models;

use App\Models\GuestUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class GuestUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_factory_creates_valid_guest_user(): void
    {
        $guestUser = GuestUser::factory()->create();

        $this->assertInstanceOf(GuestUser::class, $guestUser);
        $this->assertNotEmpty($guestUser->name, 'GuestUser name should not be empty');
        $this->assertNotEmpty($guestUser->date_of_birth, 'GuestUser date_of_birth should not be empty');
        $this->assertNotEmpty($guestUser->created_at, 'GuestUser created_at should not be empty');
        $this->assertNotEmpty($guestUser->updated_at, 'GuestUser updated_at should not be empty');
        $this->assertIsString($guestUser->name, 'GuestUser name should be a string');
    }


    public function test_guest_user_model_properties(): void
    {
        $guestUser = new GuestUser([
            'name' => 'Jimmy Ruan',
            'date_of_birth' => '1980-01-01',
        ]);
        $this->assertEquals('Jimmy Ruan', $guestUser->name);
        $this->assertEquals('1980-01-01', $guestUser->date_of_birth);
    }
}
