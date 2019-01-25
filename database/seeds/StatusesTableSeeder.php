<?php

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = ['1', '2', '3'];
        //app() 方法来获取一个 Faker 容器 的实例
        $faker = app(Faker\Generator::class);
        //each 和 map 函数最大的区别在于，each 函数是对每个元素的处理逻辑，且没有返回新的数组
        $statuses = factory(Status::class)->times(100)->make()->each(function ($status) use ($faker, $user_ids) {
            //借助 randomElement 方法来取出用户 id 数组中的任意一个元素并赋值给微博的 user_id
            $status->user_id = $faker->randomElement($user_ids);
        });

        Status::insert($statuses->toArray());
    }
}
