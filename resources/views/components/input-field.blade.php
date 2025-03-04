<div x-data="{ localMessage: $wire.entangle('message') }" class="nw-border nw-border-gray-200 nw-rounded-lg">
    <div class="nw-overflow-hidden">
        <label for="message" class="nw-sr-only">
            {{ $waiting ? 'thinking' : 'type your question here' }}
        </label>
        <div class="nw-relative">
            <textarea id="message"
                      x-model="localMessage"
                      x-on:keydown.enter.prevent="$wire.send()"
                      wrap="soft"
                      cols="10"
                      maxlength="100"
                      @disabled($waiting)
                      class="nw-block nw-w-full nw-resize-none nw-border-0 nw-bg-transparent nw-text-gray-900 placeholder:nw-text-gray-400 focus:nw-ring-0 nw-p-2 sm:nw-leading-6"
                      placeholder="{{ $waiting ? '' : 'type your question here' }}"></textarea>
            <div class="nw-absolute nw-bottom-1 nw-right-1 nw-text-xs nw-text-gray-400">
                <span x-text="`${localMessage.length}/100`"></span>
            </div>
        </div>
    </div>
</div>
