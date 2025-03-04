<div class="nw-pb-5">
    <div class="nw-h-80 nw-overflow-auto nw-flex nw-flex-col-reverse">
        @if($waiting)
            <div class="nw-w-5/6 nw-mt-3 nw-text-left">
                <div class="nw-rounded-md nw-py-2 nw-px-2 nw-bg-gray-100 nw-w-fit nw-h-6">
                    @include('chattera::components.loaders.dots')
                </div>
            </div>
        @endif

        @foreach($messages->reverse() as $message)
            <div wire:key="{{ $message->id }}"
                 class="{{ $message->role == 'assistant' ? 'nw-text-left' : 'nw-flex nw-justify-end nw-ml-auto nw-text-right' }} nw-w-5/6 nw-mt-3">
                <div class="nw-rounded-md nw-py-1 nw-px-2 {{ $message->role == 'assistant' ? 'nw-bg-gray-100 nw-text-gray-900' : 'nw-bg-blue-50 nw-text-indigo-900' }} nw-w-fit">
                    @if($message->is_new)
                        <div x-data="{ text: '', textArray: ['{{ str_replace(["\r", "\n"], [' ', '\\n'], addslashes($message->content)) }}'], charIndex: 0, typewriter: null }">
                            <div x-init="
                                let current = textArray[0];
                                typewriter = setInterval(() => {
                                    text = current.substring(0, charIndex);
                                    charIndex++;
                                    if (charIndex > current.length) clearInterval(typewriter);
                                }, 25);">
                                <p class="nw-break-words" x-text="text"></p>
                            </div>
                        </div>
                    @else
                        <p>{{ $message->content }}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
