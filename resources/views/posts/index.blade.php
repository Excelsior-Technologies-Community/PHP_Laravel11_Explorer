<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Scout Explorer Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800">

    <div class="max-w-6xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-black text-slate-900">ES EXPLORER</h1>
            <a href="{{ route('posts.create') }}" class="bg-blue-600 text-white px-5 py-2 rounded-xl font-bold hover:bg-blue-700 transition">+ Create Post</a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-100 border border-green-400 text-green-700 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8 relative">
            <form action="{{ route('posts.index') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text" id="search-input" name="search" autocomplete="off" placeholder="Search posts..." value="{{ $search ?? '' }}" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:border-blue-500">
                    <div id="autocomplete-box" class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-xl shadow-lg z-50 hidden max-h-60 overflow-y-auto"></div>
                </div>
                @if(!empty($category))
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <button type="submit" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-slate-800 transition">Search</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">Categories</h3>
                <div class="space-y-2">
                    <a href="/?search={{ $search ?? '' }}" class="flex justify-between items-center p-2 rounded-lg text-sm font-semibold {{ empty($category) ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span>All Categories</span>
                    </a>
                    @foreach($facets as $bucket)
                        <a href="/?search={{ $search ?? '' }}&category={{ $bucket->category }}" class="flex justify-between items-center p-2 rounded-lg text-sm font-semibold {{ ($category ?? '') === $bucket->category ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                            <span>{{ $bucket->category }}</span>
                            <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-md font-bold">{{ $bucket->count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-3 space-y-6">
                @forelse($posts as $post)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 relative">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider bg-blue-50 px-2.5 py-1 rounded-md">{{ $post->category }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('posts.edit', $post->id) }}" class="text-xs text-yellow-600 hover:underline font-semibold">Edit</a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline font-semibold">Delete</button>
                                </form>
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 mt-3 mb-2 post-title">{{ $post->title }}</h2>
                        <p class="text-slate-600 leading-relaxed post-content">{{ $post->content }}</p>
                    </div>
                @empty
                    <div class="bg-white p-10 rounded-2xl shadow-sm border border-slate-200 text-center text-slate-400 font-medium">No results found.</div>
                @endforelse

                <div class="pt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let searchTerm = "{{ $search ?? '' }}";
            if (searchTerm) {
                let regex = new RegExp('(' + searchTerm + ')', 'gi');
                $('.post-title, .post-content').each(function() {
                    $(this).html($(this).html().replace(regex, '<mark class="bg-yellow-200 text-slate-900 p-0.5 rounded">$1</mark>'));
                });
            }

            $('#search-input').on('input', function() {
                let query = $(this).val();
                if (query.length < 2) {
                    $('#autocomplete-box').hide().html('');
                    return;
                }

                $.ajax({
                    url: "{{ route('posts.autocomplete') }}",
                    method: "GET",
                    data: { search: query },
                    success: function(data) {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                html += `<div class="p-3 hover:bg-slate-50 cursor-pointer text-sm font-medium border-b border-slate-100 last:border-0 autocomplete-item" data-title="${item.title}">${item.title}</div>`;
                            });
                            $('#autocomplete-box').show().html(html);
                        } else {
                            $('#autocomplete-box').hide().html('');
                        }
                    }
                });
            });

            $(document).on('click', '.autocomplete-item', function() {
                $('#search-input').val($(this).data('title'));
                $('#autocomplete-box').hide();
                $(this).closest('form').submit();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#search-input').length) {
                    $('#autocomplete-box').hide();
                }
            });
        });
    </script>
</body>
</html>