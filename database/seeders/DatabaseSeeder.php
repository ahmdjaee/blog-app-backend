<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory(count: 100)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('admin'),
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'role' => 'user',
            'password' => bcrypt('user'),
        ]);

        $category =  Category::factory(count: 20)->create();

        
        for ($i = 0; $i < 100_000; $i++) {
            Post::insert([
                'title' => 'Best Practices for API Design',
                'content' => '<p>APIs are the backbone of modern web apps. Follow these best practices to design efficient APIs.</p>',
                'published' => true,
                'category_id' => $category[$i % count($category)]->id,
                'slug' => uniqid() . '-best-api-design-practices',
                'user_id' => $user[$i % count($user)]->id,
                'thumbnail' => 'thumbnails/image.png'
            ]);
        }

        Post::insert([
            [
                'title' => 'How to Learn PHP Fast',
                'content' => '<p>PHP is a popular scripting language. Here’s how you can learn it quickly and effectively.</p>',
                'published' => true,
                'category_id' => $category[0]->id,
                'slug' => uniqid() . '-learn-php-fast',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => '10 Tips for Mastering Laravel',
                'content' => '<p>Laravel makes web development easier. Check out these 10 tips for mastering it.</p>',
                'published' => false,
                'category_id' => $category[0]->id,
                'slug' => uniqid() . '-tips-master-laravel',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Understanding React Hooks',
                'content' => '<p>React hooks simplify state management. This guide will help you understand the core concepts.</p>',
                'published' => true,
                'category_id' => $category[1]->id,
                'slug' => uniqid() . '-understanding-react-hooks',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Why You Should Learn TypeScript',
                'content' => '<p>TypeScript adds type safety to JavaScript. Learn why it’s worth your time.</p>',
                'published' => false,
                'category_id' => $category[1]->id,
                'slug' => uniqid() . '-learn-typescript',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'CSS Grid vs Flexbox: Which to Use?',
                'content' => '<p>Both CSS Grid and Flexbox are powerful layout tools. Let’s compare them and see when to use each one.</p>',
                'published' => true,
                'category_id' => $category[2]->id,
                'slug' => uniqid() . '-css-grid-vs-flexbox',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Best Practices for API Design',
                'content' => '<p>APIs are the backbone of modern web apps. Follow these best practices to design efficient APIs.</p>',
                'published' => true,
                'category_id' => $category[2]->id,
                'slug' => uniqid() . '-best-api-design-practices',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'What’s New in ECMAScript 2023',
                'content' => '<p>ECMAScript 2023 introduces new features to JavaScript. Find out what’s new in the latest version.</p>',
                'published' => false,
                'category_id' => $category[3]->id,
                'slug' => uniqid() . '-ecmascript-2023',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Understanding the Event Loop in JavaScript',
                'content' => '<p>The event loop is a fundamental part of JavaScript’s asynchronous behavior. Here’s how it works.</p>',
                'published' => true,
                'category_id' => $category[3]->id,
                'slug' => uniqid() . '-event-loop-javascript',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Building Scalable Web Apps with Node.js',
                'content' => '<p>Node.js is great for building scalable web applications. Learn how to get started.</p>',
                'published' => false,
                'category_id' => $category[4]->id,
                'slug' => uniqid() . '-scalable-web-apps-nodejs',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ],
            [
                'title' => 'Introduction to Microservices Architecture',
                'content' => '<p>Microservices are popular for large-scale applications. This article introduces the core concepts.</p>',
                'published' => true,
                'category_id' => $category[4]->id,
                'slug' => uniqid() . '-intro-microservices-architecture',
                'user_id' => $user[0]->id,
                'thumbnail' => 'thumbnails/image.png'
            ]
        ]);

        $comment = Comment::factory(count: 10)->create();

       
    }
}
