@extends('layouts.admin')

@section('title', 'Meal Time Settings')

@section('content')
<div class="space-y-6">
    <h1 class="text-3xl font-bold text-gray-900">Meal Time Settings</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($settings as $setting)
            <div class="bg-white shadow-lg rounded-xl overflow-hidden">
                <div class="bg-gradient-to-r @if($setting->meal_type === 'breakfast') from-yellow-400 to-orange-400 @elseif($setting->meal_type === 'lunch') from-blue-400 to-indigo-400 @else from-purple-400 to-pink-400 @endif px-6 py-4">
                    <h2 class="text-2xl font-bold text-white capitalize">{{ $setting->meal_type }}</h2>
                </div>

                <form method="POST" action="{{ route('admin.meal-times.update', $setting->meal_type) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" required
                               value="{{ old('start_time', \Carbon\Carbon::parse($setting->start_time)->format('H:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" required
                               value="{{ old('end_time', \Carbon\Carbon::parse($setting->end_time)->format('H:i')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active_{{ $setting->meal_type }}" value="1" 
                               {{ $setting->is_active ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active_{{ $setting->meal_type }}" class="ml-2 block text-sm text-gray-700">
                            Active
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition">
                        Update Settings
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    These settings control when employees can check in for each meal. Only active meal times will appear on the attendance page.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
