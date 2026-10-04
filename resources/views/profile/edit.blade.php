{{-- resources/views/profile/edit.blade.php --}}
@extends('gentelella::page')

@section('title', 'Profile')
@section('page_key', 'profile')
@section('breadcrumb', 'Home > Profile')

@section('content')
    <x-gentelella::page-header title="Profile" pretitle="Account Settings" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Update Profile Information --}}
        <div class="lg:col-span-2">
            <x-gentelella::card title="Profile Information">
                <p class="text-sm text-gray-600 mb-4">
                    Update your account's name and email address.
                </p>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name', $user->name) }}" required autofocus
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email', $user->email) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Save
                        </button>

                        @if (session('status') === 'profile-updated')
                            <span class="text-sm text-green-600">Saved.</span>
                        @endif
                    </div>
                </form>
            </x-gentelella::card>
        </div>

        {{-- Update Password --}}
        <div>
            <x-gentelella::card title="Update Password">
                <p class="text-sm text-gray-600 mb-4">
                    Ensure your account uses a long, random password to stay secure.
                </p>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input id="current_password" name="current_password" type="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        @error('current_password', 'updatePassword')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input id="password" name="password" type="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                        @error('password', 'updatePassword')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Update
                        </button>

                        @if (session('status') === 'password-updated')
                            <span class="text-sm text-green-600">Updated.</span>
                        @endif
                    </div>
                </form>
            </x-gentelella::card>
        </div>
    </div>

    {{-- Delete Account --}}
    <div class="mt-6">
        <x-gentelella::card title="Delete Account">
            <p class="text-sm text-gray-600 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted.
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('Are you sure you want to delete your account?');">
                @csrf
                @method('delete')

                <div class="max-w-md">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>
                    <input id="delete_password" name="password" type="password"
                           placeholder="Enter your password to confirm"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    @error('password', 'userDeletion')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="mt-4 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Delete Account
                </button>
            </form>
        </x-gentelella::card>
    </div>
@endsection