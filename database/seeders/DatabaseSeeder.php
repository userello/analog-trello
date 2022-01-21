<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Desk;
use App\Models\DeskList;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $desks = Desk::factory(10)->create();

        DeskList::factory(20)->create();
        Card::factory(20)->create();
        Task::factory(20)->create();

//        $desks->each(function ($desk) {
//            DeskList::factory(20)->create([
//                'desk_id' => random(3)
//            ]);
//        });
//        $desks->each(function ($desk) use ($tags_id) {
//
//            $article->tags()->attach($tags_id->random(3));
//
//            Comment::factory(3)->create([
//                'article_id' => $article->id
//            ]);
//
//            State::factory(1)->create([
//                'article_id' => $article->id
//            ]);
//        });
    }
}
