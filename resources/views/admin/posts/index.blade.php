<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Moderate Posts</h2></x-slot>

  <div class="bg-white rounded-xl shadow divide-y">
    @foreach($posts as $post)
      <div class="p-4 flex items-center justify-between">
        <div>
          <div class="font-medium">{{ $post->title }}</div>
          <div class="text-sm text-gray-600">
            By {{ $post->author?->name ?? 'Unknown' }} · {{ $post->created_at->diffForHumans() }}
          </div>
        </div>
        <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
          @csrf @method('DELETE')
          <button class="px-3 py-1 rounded bg-red-600 text-white">Delete</button>
        </form>
      </div>
    @endforeach
  </div>

  <div class="mt-4">{{ $posts->links() }}</div>
</x-app-layout>
