<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Users</h2></x-slot>

  <div class="bg-white rounded-xl shadow divide-y">
    @foreach($users as $u)
      <div class="p-4 flex items-center justify-between">
        <div>
          <div class="font-medium">{{ $u->name }}</div>
          <div class="text-sm text-gray-600">{{ $u->email }}</div>
        </div>
        <div class="space-x-2">
          <a href="{{ route('admin.users.edit', $u) }}" class="px-3 py-1 rounded bg-indigo-600 text-white">Edit</a>
          <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline">
            @csrf @method('DELETE')
            <button class="px-3 py-1 rounded bg-red-600 text-white">Delete</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-4">{{ $users->links() }}</div>
</x-app-layout>
