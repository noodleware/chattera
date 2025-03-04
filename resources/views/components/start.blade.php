<div class="nw-flex nw-items-center nw-gap-x-1">
    @if (config('chattera.requires_email'))
        <input type="email"
               name="email"
               wire:model="email"
               required
               id="email"
               aria-label="Email"
               class="nw-block nw-rounded-md nw-bg-white nw-px-2.5 nw-py-1.5 nw-text-sm nw-text-gray-900 placeholder:nw-text-gray-400 focus:nw-outline focus:nw-outline-2 focus:nw--outline-offset-2 focus:nw-outline-indigo-600"
               placeholder="Email Address">
    @endif
    <div>
        <button type="button"
                wire:click="start"
                class="nw-rounded-md nw-bg-white nw-px-2.5 nw-py-1.5 nw-text-sm nw-font-semibold nw-text-gray-900 nw-shadow-sm nw-ring-1 nw-ring-inset nw-ring-gray-300 hover:nw-bg-gray-50">
            {!! config('chattera.ui.header.start_chat_label') !!}
        </button>
    </div>
</div>