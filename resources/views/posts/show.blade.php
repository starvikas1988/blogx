<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">{{ $post->title }}</h2>
  </x-slot>

  <article class="p-4 bg-white rounded-xl shadow">
    <p class="text-sm text-gray-600">
      By {{ $post->author?->name ?? 'Unknown' }} · {{ $post->created_at->format('d M Y') }}
    </p>
    <div class="mt-4 whitespace-pre-line">{{ $post->content }}</div>
  </article>

  @auth
  <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-6">
    @csrf
    <textarea name="body" rows="3" class="w-full border rounded p-3" placeholder="Add a comment..."></textarea>
    <button class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded">Comment</button>
  </form>
  @endauth

  <section class="mt-6 space-y-3">
    @foreach($post->comments as $c)
      <div class="p-3 bg-white rounded shadow text-sm">
        <div class="font-medium">
          {{ $c->author?->name ?? 'User' }}
          <span class="text-gray-500">· {{ $c->created_at->diffForHumans() }}</span>
        </div>
        <div>{{ $c->body }}</div>
        @if(auth()->id()===$c->user_id || auth()->user()?->hasRole('Admin'))
          <form method="POST" action="{{ route('comments.destroy', [$post, $c]) }}" class="mt-1">
            @csrf @method('DELETE')
            <button class="text-red-600">Delete</button>
          </form>
        @endif
      </div>
    @endforeach
  </section>
</x-app-layout>
