@if(config('chattera.ui.links.terms.show'))
    <div class="nw-px-5 nw-py-2 nw-space-y-1 nw-border-t nw-border nw-border-gray-200 nw-leading-none">
        <div class="nw-text-xs nw-text-gray-500">{!! formatTermsAndConditionText() !!}</div>
    </div>
@endif
