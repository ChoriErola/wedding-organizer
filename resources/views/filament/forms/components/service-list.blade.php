<div class="space-y-2">
    @foreach(explode("\n", $getState() ?: '') as $service)
        @if(trim($service))
            <div class="text-sm">• {{ $service }}</div>
        @endif
    @endforeach
</div>
