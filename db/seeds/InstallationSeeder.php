<?php

use Birdmin\Role;
use Birdmin\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Birdmin\Permission;

class InstallationSeeder extends Seeder
{
    /**
     * Installs the basic environment.
     * Includes super user and basic role assignment.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name'    => 'Mike',
            'last_name'     => 'Adamczyk',
            'email'         => 'mike@bom.us',
            'password'      => Hash::make('password'),
            'position'      => 'Web Developer',
            'affiliation'   => 'Brightstar Corporation',
            'website'       => 'http://bom.us'
        ]);

        // Sample user.
        $admin = User::create([
            'first_name'    => 'Robin',
            'last_name'     => 'Bird',
            'email'         => 'test@bom.us',
            'password'      => Hash::make('password')
        ]);

        $roles = [
            ['name'=>'Super User', 'description'=>'Provides full access to the application.'],
            ['name'=>'Administrator', 'description'=>'Provides non-system content and object management.'],
            ['name'=>'Editor', 'description'=>'Provides non-system content-management access.'],
        ];
        foreach($roles as $data) {
            $role = Role::create($data);
        }

        // Assign the role to the admin user.
        $user->assignRole('Super User');
        $admin->assignRole('Administrator');
    }
}
