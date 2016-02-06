<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Birdmin\Permission;
use Birdmin\User;
use Birdmin\Page;
use Birdmin\Role;

class PermissionTest extends TestCase
{
    /**
     * Test if the permission index is being set correctly.
     */
    public function test_permission_index()
    {
        Permission::index();
        $index = Permission::getIndex();
        // Index there and is the right type?
        $this->assertInstanceOf('Birdmin\Collections\PermissionCollection', $index);
        $this->assertGreaterThan(0,$index->count());

        // Basic permission exists?
        $this->assertInstanceOf('Birdmin\Permission', $index->lookup('view', 'Birdmin\Page'));

        // Test the right permission being returned.
        $permission = $index->lookup('view', 'Birdmin\Page');
        $this->assertEquals('view', $permission->ability);
        $this->assertEquals('Birdmin\Page', $permission->object);

        // Non-existent permission returning null with bogus ability?
        $this->assertNull($index->lookup('wrong', 'Birdmin\Page'));

        // Non-existent permission returning null with bogus model?
        $this->assertNull($index->lookup('view', 'Wrong'));
    }

    /**
     * Test if the super user can do stuff.
     */
    public function test_super_user()
    {
        // This guy is the super user with fresh install.
        $user = User::find(1);
        $this->assertTrue($user->hasRole('Super User'));

        // Because he's a super user, the ModelPolicy should allow him to do anything.
        $this->assertTrue($user->can('view', 'Birdmin\Page'));
        $this->assertTrue($user->can('edit', 'Birdmin\Page'));
        $this->assertTrue($user->can('delete', 'Birdmin\Page'));
        $this->assertTrue($user->can('create', 'Birdmin\Page'));
        $this->assertTrue($user->can('manage', 'Birdmin\User'));

        // Non-existent permissions should always return true when tested.
        $this->assertTrue($user->can('wrong', 'Birdmin\Page'));

    }

    /**
     * Test basic user authorizations, with models.
     * This should deal directly with the ModelPolicy class.
     */
    public function test_basic_user_auth()
    {
        // User 2 has permission to do a couple things.
        $user = User::find(2);
        $this->assertTrue($user->hasRole(Role::getByName('Administrator')));

        // The models we'll test.
        $page = Page::find(1);

        $this->assertTrue($user->can('view', $page));
        $this->assertFalse($user->can('delete', $page));

        // User model is a managed class. The user doesn't have the manage permission.
        // So, They shouldn't be able to edit a user that doesn't belong to them.
        $testUser = User::find(1);
        $this->assertFalse($user->can('edit', $testUser));

        // But they can edit themselves.
        $this->assertTrue($user->can('edit', $user));
    }
}
