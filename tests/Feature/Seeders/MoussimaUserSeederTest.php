<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Database\Seeders\MoussimaUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MoussimaUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_the_moussima_administrator_account(): void
    {
        $this->seed(MoussimaUserSeeder::class);

        $user = User::where('email', 'moussima2000@yahoo.fr')->firstOrFail();

        $this->assertSame('Moussima', $user->name);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole('Administrateur'));
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_it_does_not_duplicate_or_overwrite_an_existing_account(): void
    {
        $this->seed(MoussimaUserSeeder::class);

        $user = User::where('email', 'moussima2000@yahoo.fr')->firstOrFail();
        $user->update([
            'name' => 'Nom personnalisé',
            'password' => Hash::make('mot-de-passe-change'),
        ]);

        $this->seed(MoussimaUserSeeder::class);

        $user->refresh();
        $this->assertSame(1, User::where('email', 'moussima2000@yahoo.fr')->count());
        $this->assertSame('Nom personnalisé', $user->name);
        $this->assertTrue(Hash::check('mot-de-passe-change', $user->password));
    }
}
