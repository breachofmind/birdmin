<?php

use Birdmin\Permission;
use Birdmin\Core\Model;
use Illuminate\Database\Seeder;
use Birdmin\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Install default permissions for each model object.
     *
     * @return void
     */
    public function run()
    {
        $created = [];

        $config = Model::$config;

        foreach ($config as $class=>$data)
        {
            foreach ((array)$data['permissions'] as $permission) {
                $action = strtolower($permission);
                if ( Permission::where('object', $class)
                    ->where('ability', $action)
                    ->exists() ) {
                    continue;
                }
                $created[] = Permission::create([
                    'object' => $class,
                    'ability' => $action,
                    'description' => $this->describe($class,$action)
                ]);
            }
        }
        $this->grantAbilities();


        echo sizeof($created)." Permission objects created.\n";
    }

    /**
     * Quick description of the permission verb.
     * @param $class string
     * @param $action string
     * @return string
     */
    protected function describe ($class,$action)
    {
        $lookup = [
            'view' => 'Can view '.$class::plural(),
            'edit' => 'Can edit '.$class::plural(),
            'delete' => 'Can delete or unassign '.$class::plural(),
            'create' => 'Can create new '.$class::plural(),
            'manage' => 'Can manage other '.$class::plural()
        ];
        return isset($lookup[$action]) ? $lookup[$action] : "";
    }

    protected function grantAbilities()
    {
        $administrator = Role::getByName('Administrator');
        $administrator->grantAbility('view','Birdmin\Page');
        echo "Granted permissions for 'Administrator' role.\n";
    }


}
