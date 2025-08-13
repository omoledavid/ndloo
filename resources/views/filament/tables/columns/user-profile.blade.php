<div style="gap:8px;" class="flex items-center mt-2 mb-2">
    <!-- Profile Picture -->
    <div class="flex-shrink-0 relative">
        @if($getRecord()->avatar)
            <img 
                src="{{ asset($getRecord()->avatar) }}" 
                alt="{{ $getRecord()->firstname }} {{ $getRecord()->lastname }}"
                class="w-11 h-11 rounded-full object-cover border-2 border-gray-200"
            />
        @else
            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold text-lg">
                {{ strtoupper(substr($getRecord()->firstname, 0, 1)) }}{{ strtoupper(substr($getRecord()->lastname, 0, 1)) }}
            </div>
        @endif
        
        <!-- Online Status Indicator -->
        <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full border-2 border-white {{ $getRecord()->is_online ? 'bg-green-500' : 'bg-red-500' }}"></div>
    </div>

    <!-- User Information -->
    <div class="flex flex-col">
        <!-- Full Name -->
        <div class="text-sm font-semibold text-gray-800 leading-tight">
            {{ $getRecord()->firstname }} {{ $getRecord()->lastname }}
        </div>
        
        <!-- Username -->
        <div class="text-sm text-gray-600 leading-tight">
            {{ '@' . $getRecord()->username }}
        </div>
        
        <!-- Phone Number -->
        <div class="text-sm text-gray-600 leading-tight">
            {{ $getRecord()->phone ?: 'No phone number' }}
        </div>
    </div>
</div>
