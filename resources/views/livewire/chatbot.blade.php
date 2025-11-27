<div>
    <div x-data="{ show: @entangle('show').live }" class="nw-z-40">
        <div x-cloak
             x-show="!show"
             class="nw-fixed nw-bottom-5 nw-right-5 nw-fill-green-500">
            <button @click="show = true"
                    type="button"
                    class="nw-inline-flex nw-items-center nw-gap-x-2 nw-rounded-md {{ config('chattera.ui.button.background', 'nw-bg-indigo-600') }} nw-px-3.5 nw-py-2.5 nw-text-sm nw-font-semibold {{ config('chattera.ui.button.text_color', 'nw-text-white') }} nw-shadow-sm hover:{{ config('chattera.ui.button.hover', 'nw-bg-indigo-500') }} focus-visible:nw-outline focus-visible:nw-outline-2 focus-visible:nw-outline-offset-2 focus-visible:nw-outline-indigo-600">
                @include('chattera::components.icons.question-mark-circle', ['class' => 'nw-w-5 nw-h-5'])
                {!! config('chattera.ui.button.label') !!}
            </button>
        </div>
        <div x-cloak
             x-show="show"
             class="nw-relative nw-z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="show"
                 x-transition:enter="nw-ease-out nw-duration-300"
                 x-transition:enter-start="nw-opacity-0"
                 x-transition:enter-end="nw-opacity-100"
                 x-transition:leave="nw-ease-in nw-duration-200"
                 x-transition:leave-start="nw-opacity-100"
                 x-transition:leave-end="nw-opacity-0"
                 class="nw-fixed nw-inset-0 nw-bg-gray-500 nw-bg-opacity-50 nw-transition-opacity"></div>
            <div class="nw-fixed nw-inset-0 nw-z-10 nw-w-screen nw-overflow-y-auto">
                <div class="nw-flex nw-min-h-full nw-items-end nw-justify-end md:nw-pb-5 md:nw-mr-5">
                    <div x-show="show"
                         x-transition:enter="nw-ease-out nw-duration-300"
                         x-transition:enter-start="nw-opacity-0 nw-translate-y-full nw-translate-x-full sm:nw-translate-y-0 sm:nw-translate-x-0 sm:nw-scale-95"
                         x-transition:enter-end="nw-opacity-100 nw-translate-y-0 nw-translate-x-0 sm:nw-scale-100"
                         x-transition:leave="nw-ease-in nw-duration-200"
                         x-transition:leave-start="nw-opacity-100 nw-translate-y-0 nw-translate-x-0 sm:nw-scale-100"
                         x-transition:leave-end="nw-opacity-0 nw-translate-y-full nw-translate-x-full sm:nw-translate-y-0 sm:nw-translate-x-0 sm:nw-scale-95"
                         class="nw-relative nw-transform nw-overflow-hidden nw-rounded-lg nw-bg-white nw-transition-all nw-w-full sm:nw-max-w-lg">
                        {{-- Header --}}
                        @include('chattera::components.header')

                        @if($started)
                            <div class="nw-px-5 nw-pb-5">
                                {{-- Message List --}}
                                @include('chattera::components.message-list')

                                {{-- Input Field --}}
                                @include('chattera::components.input-field')
                            </div>
                        @endif

                        {{-- Footer --}}
                        @include('chattera::components.footer')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        Livewire.on('messageAdded', () => {
        @this.getResponse();
        });
    </script>
    @endscript
</div>
