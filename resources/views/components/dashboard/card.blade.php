@props(['title','value','icon'])

<div class="bg-white rounded-xl p-4 shadow-sm">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $title }}</p>
            <p class="text-2xl font-bold">{{ $value }}</p>
        </div>
        <div class="text-green-600">
            <i data-feather="{{ $icon }}" class="w-6 h-6"></i>
        </div>
    </div>
</div>
