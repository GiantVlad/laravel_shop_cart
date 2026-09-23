<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Admin;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    public function testAdminFactoryCreatesAnAdmin(): void
    {
        $admin = Admin::factory()->create();

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertDatabaseHas('admins', ['email' => $admin->email]);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $admin->password));
    }
}
