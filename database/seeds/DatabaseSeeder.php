<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //临时取消批量赋值（mass assignment）保护
        Model::unguard();

        $this->call(UsersTableSeeder::class);

        //恢复批量赋值（mass assignment）保护
        Model::reguard();
    }
}
