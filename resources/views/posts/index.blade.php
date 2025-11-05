<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Posts</h2>
    <form method="GET" class="mt-2">
      <input name="q" value="{{ request('q') }}" class="border rounded p-2" placeholder="Search title">
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
    </form>
  </x-slot>

  <div class="space-y-4">
    @foreach($posts as $post)
      <article class="p-4 bg-white rounded-xl shadow">
        <h3 class="text-xl font-semibold">
          <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
        </h3>
        <p class="text-sm text-gray-600">
          By {{ $post->author?->name ?? 'Unknown' }} · {{ $post->created_at->diffForHumans() }}
        </p>
      </article>
    @endforeach

    {{ $posts->withQueryString()->links() }}
  </div>
</x-app-layout>
