<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Edit User</h2></x-slot>

  <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4 bg-white p-6 rounded-xl shadow">
    @csrf @method('PUT')
    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input name="name" class="w-full border rounded p-2" value="{{ old('name', $user->name) }}" required>
      @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Email</label>
      <input name="email" type="email" class="w-full border rounded p-2" value="{{ old('email', $user->email) }}" required>
      @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Optional role dropdown (requires spatie roles) --}}
    @if(class_exists(\Spatie\Permission\Models\Role::class))
      <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role" class="w-full border rounded p-2">
          @foreach(\Spatie\Permission\Models\Role::all() as $role)
            <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>
          @endforeach
        </select>
      </div>
    @endif

    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
  </form>
</x-app-layout>
