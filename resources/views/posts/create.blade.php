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
