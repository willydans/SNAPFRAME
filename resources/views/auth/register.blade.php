@extends('layout')

@section('content')
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Buat akun baru</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm bg-white p-6 shadow rounded-lg">
    <form class="space-y-6" action="{{ route('register') }}" method="POST">
      @csrf
      
      <!-- Nama -->
      <div>
        <label class="block text-sm font-medium leading-6 text-gray-900">Nama Lengkap</label>
        <div class="mt-2">
          <input name="name" type="text" required class="block w-full rounded-md border-0 py-1.5 px-3 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
        <div class="mt-2">
          <input name="email" type="email" required class="block w-full rounded-md border-0 py-1.5 px-3 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        <div class="mt-2">
          <input name="password" type="password" required class="block w-full rounded-md border-0 py-1.5 px-3 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Password</label>
        <div class="mt-2">
          <input name="password_confirmation" type="password" required class="block w-full rounded-md border-0 py-1.5 px-3 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6">
        </div>
      </div>

      <div>
        <button type="submit" class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500">Daftar</button>
      </div>
    </form>
  </div>
</div>
@endsection