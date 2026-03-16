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

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
@endif

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
