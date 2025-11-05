<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Create Post</h2></x-slot>

  <form method="POST" action="{{ route('posts.store') }}" class="space-y-4 bg-white p-6 rounded-xl shadow">
    @csrf
    <div>
      <label class="block text-sm font-medium mb-1">Title</label>
      <input name="title" class="w-full border rounded p-2" value="{{ old('title') }}" required>
      @error('title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Content</label>
      <textarea name="content" rows="6" class="w-full border rounded p-2" required>{{ old('content') }}</textarea>
      @error('content') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
  </form>
</x-app-layout>
