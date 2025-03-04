<div class="nw-bg-gradient-to-r {{ config('chattera.ui.header.gradient.from', 'nw-from-indigo-600') }} {{ config('chattera.ui.header.gradient.to', 'nw-to-indigo-900') }} nw-px-5 nw-py-6 {{ config('chattera.ui.header.text_color', 'nw-text-white') }} nw-space-y-4">
    <div class="nw-flex nw-justify-between nw-items-center">
        <div class="nw-inline-flex nw-items-center nw-gap-x-2 nw-text-base nw-font-semibold nw-leading-6">
            @include('chattera::components.icons.question-mark-circle', ['class' => 'nw-w-5 nw-h-5'])
            <div class="nw-text-lg">{!! config('chattera.ui.header.title') !!}</div>
        </div>
        <div @click="show = false">
            @include('chattera::components.icons.x-mark', ['class' => 'nw-h-6 nw-w-6 nw-shrink-0 nw-text-indigo-50 nw-cursor-pointer'])
        </div>
    </div>
    <div>{!! config('chattera.ui.header.subtitle') !!}</div>
    {{-- Start --}}
    @if(! $started)
        @include('chattera::components.start')
    @endif
    {{-- Support Text --}}
    @if(config('chattera.ui.links.support.show'))
        <div>{!! formatSupportText() !!}</div>
    @endif
</div>