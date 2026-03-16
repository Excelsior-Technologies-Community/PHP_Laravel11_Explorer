# PHP_Laravel11_Explorer

## Introduction

This project demonstrates how to integrate a powerful search engine into a Laravel application using Elasticsearch together with Laravel Scout and the Explorer package.

Laravel is widely used for building modern web applications, and while its default database queries work well for many use cases, full-text search across large datasets can become slow when using traditional SQL queries.

To solve this problem, search engines such as Elasticsearch are used to index and search data efficiently.

In this project, posts created in the application are stored in a MySQL database and automatically indexed into Elasticsearch. When a user performs a search, the request is processed through Laravel Scout and executed by Elasticsearch, which returns highly optimized search results.

This repository serves as a practical learning example for developers who want to understand how to integrate a scalable search engine into a Laravel application.

---

## Project Overview

The PHP_Laravel11_Explorer application is a simple blog-style project that demonstrates how to implement full-text search functionality using Elasticsearch in a Laravel environment.

The system allows users to create posts consisting of a title and content. These posts are stored in a MySQL database and are indexed in Elasticsearch through Laravel Scout and the Explorer driver.

Once indexed, the application can perform fast keyword-based searches across the stored posts. Instead of querying the database directly, search requests are handled by Elasticsearch, which is optimized for text search and large datasets.

---

## Core Features

• Create posts with title and content
• Store post data in a MySQL database
• Automatically index posts into Elasticsearch
• Perform full-text search using Laravel Scout
• Display search results in a simple user interface

---

## Technologies Used

* PHP
* Laravel 11
* MySQL
* Laravel Scout
* Explorer (Scout driver for Elasticsearch)
* Elasticsearch 7.x

---

## Installation Guide

## Step 1 — Create Laravel 11 Project

Create the project using Composer:

```bash
composer create-project laravel/laravel PHP_Laravel11_Explorer "11.*"
```
Navigate to the project directory:

```bash
cd PHP_Laravel11_Explorer
```
---

## Step 2: Configure Database

Open the `.env` file and configure your MySQL database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel11_explorer
DB_USERNAME=root
DB_PASSWORD=
```

Then Run Migration Command:

```bash
php artisan migrate
```

---

## Step 3 — Install Laravel Scout

Laravel Scout provides a driver-based system for adding search functionality to Eloquent models.

Install Scout:

```bash
composer require laravel/scout
```

Publish Scout configuration:

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

This will create:

```
config/scout.php
```

---

## Step 4 — Install Explorer

Install Explorer package:

```bash
composer require jeroen-g/explorer
```

Explorer acts as an Elasticsearch driver for Laravel Scout and allows advanced search queries.

Publish Explorer configuration:

```bash
php artisan vendor:publish --tag=explorer.config
```

This creates:

```
config/explorer.php
```

---

## Step 5: Elasticsearch Setup

This project requires Elasticsearch to be installed and running locally.

### Download Elasticsearch

Download Elasticsearch from the official website:

```
https://www.elastic.co/downloads/elasticsearch
```

Download version **7.x** for compatibility.


### Install Elasticsearch (Windows)

Extract the downloaded ZIP file.

Example folder:

```
C:\elasticsearch-7.17.20
```

---

### Start Elasticsearch

Navigate to the Elasticsearch folder:

```bash
cd elasticsearch-7.17.20
```

Run the server:

```bash
bin\elasticsearch.bat
```

Elasticsearch will start on:

```
http://localhost:9200
```

### Verify Elasticsearch is Running

Open the following URL in your browser:

http://localhost:9200

If Elasticsearch is running correctly, you will see a JSON response similar to:

```
{
  "name" : "node-1",
  "cluster_name" : "elasticsearch",
  "version" : {
    "number" : "7.17.20"
  }
}
```

---

## Step 6 — Configure Scout Driver

Open:

config/scout.php

Change driver:

```php
'driver' => env('SCOUT_DRIVER', 'elastic'),
```

Then add in .env:

```.env
SCOUT_DRIVER=elastic
```

---

## Step 7 — Configure Explorer Index

Open:

config/explorer.php

Example configuration:

```php
<?php

declare(strict_types=1);

use App\Models\Post;

return [
    /*
     * There are different options for the connection. Since Explorer uses the Elasticsearch PHP SDK
     * under the hood, all the host configuration options of the SDK are applicable here. See
     * https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/configuration.html
     */
    'connection' => [
        'host' => 'localhost',
        'port' => '9200',
        'scheme' => 'http',
    ],

    /**
     * The default index settings used when creating a new index. You can override these settings
     * on a per-index basis by implementing the IndexSettings interface on your model or defining
     * them in the index configuration below.
     */
    'default_index_settings' => [
        //'index' => [],
        //'analysis' => [],
    ],

    /**
     * An index may be defined on an Eloquent model or inline below. A more in depth explanation
     * of the mapping possibilities can be found in the documentation of Explorer's repository.
     */
 'indexes' => [

    'posts' => [
        'settings' => [],
        'mapping' => [
            'properties' => [
                'id' => ['type' => 'keyword'],
                'title' => ['type' => 'text'],
                'content' => ['type' => 'text'],
            ],
        ],
    ],

],

    /**
     * You may opt to keep the old indices after the alias is pointed to a new index.
     * A model is only using index aliases if it implements the Aliased interface.
     */
    'prune_old_aliases' => true,

    /**
     * When set to true, sends all the logs (requests, responses, etc.) from the Elasticsearch PHP SDK
     * to a PSR-3 logger. Disabled by default for performance.
     */
    'logging' => env('EXPLORER_ELASTIC_LOGGER_ENABLED', false),
    'logger' => null,
];
```

This file defines the Elasticsearch index and its fields.

---

## Step 8 — Create Post Model

Generate model and migration:

```bash
php artisan make:model Post -m
```

---

## Step 9 — Migration

Open migration file:

database/migrations/create_posts_table.php

Update:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

Run migration:

```bash
php artisan migrate
```
---

## Step 10 — Update Model

Open:

app/Models/Post.php

Add Scout searchable trait.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use JeroenG\Explorer\Application\Explored;

class Post extends Model implements Explored
{
    use Searchable;

    protected $fillable = [
        'title',
        'content'
    ];

    public function searchableAs(): string
    {
        return 'posts';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
        ];
    }

    public function mappableAs(): array
    {
        return [
            'id' => [
                'type' => 'keyword'
            ],
            'title' => [
                'type' => 'text'
            ],
            'content' => [
                'type' => 'text'
            ]
        ];
    }
}
```

---

## Step 11 — Create Controller

Generate controller:

```bash
php artisan make:controller PostController
```

Open:

app/Http/Controllers/PostController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::latest()->get();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required'
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect('/')->with('success','Post Created Successfully');
    }

    public function search(Request $request)
    {
        $posts = Post::search($request->search)->get();

        return view('posts.index', compact('posts'));
    }

}
```

---

## Step 12 — Create Routes

Open:

routes/web.php

Add:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', [PostController::class,'index']);

Route::get('/posts/create',[PostController::class,'create'])->name('posts.create');

Route::post('/posts/store',[PostController::class,'store'])->name('posts.store');

Route::get('/search',[PostController::class,'search'])->name('posts.search');
```
---

## Step 11 — Create Blade Files

Create folder:

```
resources/views/posts
```

### index.blade.php

File: resources/views/posts/index.blade.php

```blade
<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laravel Explorer Search</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="max-w-5xl mx-auto p-6">


<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        Laravel Explorer Search
    </h1>

    <a href="{{ route('posts.create') }}"
       class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
        + Create Post
    </a>
</div>

<!-- Search -->
<div class="bg-white p-6 rounded-xl shadow mb-8">
    <form action="{{ route('posts.search') }}" method="GET" class="flex gap-3">
        <input
            type="text"
            name="search"
            placeholder="Search posts..."
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        <button
            type="submit"
            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
            Search
        </button>
    </form>
</div>

<!-- Posts -->
<div class="space-y-6">

    @forelse($posts as $post)

    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">

        <h2 class="text-xl font-semibold text-gray-800 mb-2">
            {{ $post->title }}
        </h2>

        <p class="text-gray-600">
            {{ $post->content }}
        </p>

    </div>

    @empty

    <div class="bg-white p-6 rounded-xl shadow text-center text-gray-500">
        No posts found.
    </div>

    @endforelse

</div>


</div>

</body>
</html>
```

### create.blade.php

File: resources/views/posts/create.blade.php

```blade
<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Post</title>

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="max-w-xl mx-auto p-6">

<div class="bg-white p-8 rounded-xl shadow">

    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        Create New Post
    </h2>

    <form action="{{ route('posts.store') }}" method="POST" class="space-y-5">

        @csrf

        <div>
            <label class="block text-gray-700 font-medium mb-1">
                Title
            </label>

            <input
                type="text"
                name="title"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter post title">
        </div>


        <div>
            <label class="block text-gray-700 font-medium mb-1">
                Content
            </label>

            <textarea
                name="content"
                rows="5"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Write your content here..."></textarea>
        </div>


        <div class="flex items-center justify-between">

            <a href="/"
               class="text-gray-600 hover:text-gray-900">
               ← Back to Posts
            </a>

            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Save Post
            </button>

        </div>

    </form>

</div>


</div>

</body>
</html>
```
---

## Step 12: Index Data into Elasticsearch

After creating posts, import them into Elasticsearch:

```bash
php artisan scout:import "App\Models\Post"
```

This command indexes all existing posts.

---

## Step 13: Verify Elasticsearch Index

Open in browser:

```
http://localhost:9200/_cat/indices?v
```

You should see the posts index.

---

## Step 14 Run the Application

Start Laravel server:

```bash
php artisan serve
```

Open the application:

```
http://127.0.0.1:8000
```

---

## Example Search

Search example:

```
http://127.0.0.1:8000/search?search=ramayana
```

---

## Output

<img src="screenshots/Screenshot 2026-03-16 122500.png" width="900">

<img src="screenshots/Screenshot 2026-03-16 122513.png" width="900">

<img src="screenshots/Screenshot 2026-03-16 122540.png" width="900">

---

## Project Structure

Below is the main directory structure of the project:

```
PHP_Laravel11_Explorer
│
├── app
│   ├── Http
│   │   └── Controllers
│   │       └── PostController.php        # Handles post creation and search
│   │
│   └── Models
│       └── Post.php                      # Post model with Scout + Explorer search
│
├── bootstrap
│
├── config
│   ├── explorer.php                      # Elasticsearch configuration
│   └── scout.php                         # Laravel Scout configuration
│
├── database
│   ├── factories
│   ├── migrations
│   │   └── xxxx_create_posts_table.php   # Migration for posts table
│   └── seeders
│
├── public
│   └── index.php
│
├── resources
│   └── views
│       └── posts
│           ├── index.blade.php           # Displays posts and search form
│           └── create.blade.php          # Form to create new posts
│
├── routes
│   └── web.php                           # Application routes
│
├── storage
│
├── tests
│
├── vendor                                # Composer dependencies
│
├── .env                                  # Environment configuration
├── composer.json                         # PHP dependencies
├── artisan                               # Laravel CLI
└── README.md
```

Search requests are processed by Laravel Scout and executed by Elasticsearch, returning fast and relevant search results.

---

Your PHP_Laravel11_Explorer Project is now ready!
