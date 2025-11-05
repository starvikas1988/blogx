<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-xl">Admin Dashboard</h2></x-slot>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="p-6 bg-white rounded-xl shadow">Users: {{ $stats['users'] }}</div>
    <div class="p-6 bg-white rounded-xl shadow">Posts: {{ $stats['posts'] }}</div>
    <div class="p-6 bg-white rounded-xl shadow">Comments: {{ $stats['comments'] }}</div>
  </div>
</x-app-layout>
