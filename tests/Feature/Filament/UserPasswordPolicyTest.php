<?php

namespace Tests\Feature\Filament;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserPasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->admin());
    }

    public function test_a_short_password_is_refused(): void
    {
        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('create')->table(), data: $this->newUser(['password' => 'a']))
            ->assertHasActionErrors(['password']);

        $this->assertFalse(User::where('email', 'nouveau@example.test')->exists());
    }

    public function test_a_password_just_under_the_floor_is_refused(): void
    {
        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('create')->table(), data: $this->newUser(['password' => 'onzecarac']))
            ->assertHasActionErrors(['password']);

        $this->assertFalse(User::where('email', 'nouveau@example.test')->exists());
    }

    public function test_a_long_enough_password_is_accepted(): void
    {
        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('create')->table(), data: $this->newUser([
                'password' => 'un-mot-de-passe-solide',
            ]))
            ->assertHasNoActionErrors();

        $this->assertTrue(
            Hash::check('un-mot-de-passe-solide', User::where('email', 'nouveau@example.test')->sole()->password),
        );
    }

    // The field is optional on edit, so the floor must not fire on an untouched password.
    public function test_editing_without_touching_the_password_still_saves(): void
    {
        $user = User::create([
            'name' => 'Agent', 'email' => 'agent@example.test', 'password' => 'un-mot-de-passe-solide',
            'role' => UserRole::Agent, 'is_active' => true,
        ]);

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('edit')->table($user), data: [
                'name' => 'Agent renommé',
                'email' => 'agent@example.test',
                'role' => UserRole::Agent->value,
                'locale' => 'fr',
                'password' => '',
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $this->assertSame('Agent renommé', $user->refresh()->name);
        $this->assertTrue(Hash::check('un-mot-de-passe-solide', $user->password));
    }

    public function test_an_edit_cannot_set_a_weak_password(): void
    {
        $user = User::create([
            'name' => 'Agent', 'email' => 'agent@example.test', 'password' => 'un-mot-de-passe-solide',
            'role' => UserRole::Agent, 'is_active' => true,
        ]);

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('edit')->table($user), data: [
                'name' => 'Agent',
                'email' => 'agent@example.test',
                'role' => UserRole::Agent->value,
                'locale' => 'fr',
                'password' => 'court',
                'is_active' => true,
            ])
            ->assertHasActionErrors(['password']);

        $this->assertTrue(Hash::check('un-mot-de-passe-solide', $user->refresh()->password));
    }

    /** @param array<string, mixed> $overrides */
    private function newUser(array $overrides = []): array
    {
        return [
            'name' => 'Nouveau',
            'email' => 'nouveau@example.test',
            'role' => UserRole::Agent->value,
            'locale' => 'fr',
            'is_active' => true,
            ...$overrides,
        ];
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => 'un-mot-de-passe-solide',
            'role' => UserRole::Admin, 'is_active' => true,
        ]);
    }
}
