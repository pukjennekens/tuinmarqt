@props(['title', 'status'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ open: false }">
    <div class="py-4 px-6 cursor-pointer flex items-center justify-between" x-on:click="open = !open">
        <h3 class="text-xl font-bold inline-flex items-center gap-2">
            @if(isset($status))
                @if($status == 'success')
                    <div class="w-2 h-2 bg-green-600 rounded-full"></div>
                @elseif($status == 'warning')
                    <div class="w-2 h-2 bg-yellow-600 rounded-full"></div>
                @elseif($status == 'danger')
                    <div class="w-2 h-2 bg-red-600 rounded-full"></div>
                @endif
            @endif

            <span>{{ $title }}</span>
        </h3>

        <i class="fa-solid fa-chevron-down transition" :class="{ 'transform rotate-180': open }"></i>
    </div>

    <div 
        class="p-6 pt-0 text-gray-900"
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 transform scale-[99%]"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-[99%]"
    >
        {{ $slot }}
    </div>
</div>