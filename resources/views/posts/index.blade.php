<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorer Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #0f172a;
            overflow: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="text-white">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <div class="w-72 bg-slate-950 border-r border-slate-800 p-6 flex flex-col justify-between">

            <div>

                <!-- Logo -->
                <div class="flex items-center gap-4 mb-12">

                    <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl shadow-lg">
                        🚀
                    </div>

                    <div>

                        <h1 class="text-2xl font-black">
                            Explorer
                        </h1>

                        <p class="text-slate-400 text-sm">
                            Laravel Elasticsearch
                        </p>

                    </div>

                </div>

                <!-- Stats -->
                <div class="glass rounded-3xl p-6 mb-6">

                    <p class="text-slate-400 mb-2">
                        Total Posts
                    </p>

                    <h2 class="text-5xl font-black text-blue-400">
                        {{ $posts->total() }}
                    </h2>

                </div>

                <!-- Search -->
                <form action="{{ route('posts.index') }}" method="GET" class="space-y-4">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..."
                        class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-5 py-4 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button class="w-full bg-blue-600 hover:bg-blue-700 py-4 rounded-2xl font-bold transition">

                        Search

                    </button>

                </form>

                <!-- Create -->
                <a href="{{ route('posts.create') }}"
                    class="block text-center mt-5 bg-emerald-500 hover:bg-emerald-600 py-4 rounded-2xl font-bold transition">

                    + Create Post

                </a>

            </div>

            <!-- Footer -->
            <div class="glass rounded-2xl p-4 text-center text-slate-400 text-sm">

                Powered by Laravel Scout & Elasticsearch

            </div>

        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Header -->
            <div class="p-8 pb-4 flex items-center justify-between">

                <div>

                    <h1 class="text-4xl font-black mb-2">
                        Dashboard
                    </h1>

                    <p class="text-slate-400">
                        Manage and search posts professionally
                    </p>

                </div>

                @if(session('success'))

                    <div class="bg-green-500/20 border border-green-500/20 text-green-300 px-6 py-3 rounded-2xl">
                        {{ session('success') }}
                    </div>

                @endif

            </div>

            <!-- Posts Area -->
            <div class="flex-1 overflow-hidden px-8">

                <div
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 h-full overflow-y-auto pb-6 scrollbar-hide">

                    @forelse($posts as $post)

                        <!-- Card -->
                        <div
                            class="glass rounded-3xl p-6 flex flex-col justify-between h-[320px] hover:scale-[1.02] transition duration-300">

                            <div>

                                <!-- Top -->
                                <div class="flex items-center justify-between mb-5">

                                    <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-xs font-semibold">
                                        POST #{{ $post->id }}
                                    </span>

                                    <span class="text-slate-500 text-sm">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>

                                </div>

                                <!-- Title -->
                                <h2 class="text-2xl font-bold mb-4 line-clamp-1">
                                    {{ $post->title }}
                                </h2>

                                <!-- Content -->
                                <p class="text-slate-400 text-sm leading-relaxed">
                                    {{ Str::limit($post->content, 120) }}
                                </p>

                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-3 mt-6">

                                <a href="{{ route('posts.edit', $post->id) }}"
                                    class="w-full text-center bg-yellow-500 hover:bg-yellow-600 py-3 rounded-xl font-bold transition">

                                    Edit

                                </a>

                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" class="w-full"
                                    onsubmit="return confirm('Delete this post?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="w-full bg-red-600 hover:bg-red-700 py-3 rounded-xl font-bold transition">

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </div>

                    @empty

                        <!-- Empty State -->
                        <div class="col-span-3 flex items-center justify-center">

                            <div class="glass rounded-3xl p-16 text-center w-full">

                                <div class="text-7xl mb-6">
                                    🔍
                                </div>

                                <h2 class="text-3xl font-black mb-3">
                                    No Posts Found
                                </h2>

                                <p class="text-slate-400">
                                    Try another keyword
                                </p>

                            </div>

                        </div>

                    @endforelse

                </div>

            </div>

            <!-- Pagination -->
            <div class="p-6 border-t border-slate-800 bg-slate-950/40">

                <div class="flex justify-center">

                    <div class="glass px-6 py-3 rounded-2xl">
                        {{ $posts->links() }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>