{{-- Virtual Try-On dialog. Opened via window.dispatchEvent(new CustomEvent('tryon:open', { detail: { id } })) --}}
@php $tryFrames = \App\Models\Product::active()->where('tryon_enabled', true)->whereNotNull('tryon_overlay')->with('primaryImage')->get()->map->tryOnPayload()->values(); @endphp
@if ($tryFrames->isNotEmpty())
<div x-data="tryOnModal({{ Js::from($tryFrames) }})" @tryon:open.window="open($event.detail?.id)" @keydown.escape.window="if (show) close()">
    <div x-cloak x-show="show" x-transition.opacity class="fixed inset-0 z-[70] flex items-end justify-center bg-[#0e0d0c]/80 backdrop-blur-sm sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="tryon-title">
        <div x-show="show" x-transition:enter="transition duration-300" x-transition:enter-start="translate-y-6 opacity-0" class="relative flex max-h-[100dvh] w-full max-w-5xl flex-col overflow-hidden bg-white sm:rounded-[1.75rem]" @click.outside="close()">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <p id="tryon-title" class="font-display text-xl">Virtual Try-On</p>
                    <p class="text-xs text-muted" x-text="frame ? frame.name : ''"></p>
                </div>
                <button type="button" @click="close()" class="grid size-10 place-items-center rounded-full hover:bg-sand" aria-label="Close virtual try-on"><x-icon name="x"/></button>
            </div>

            <div class="grid min-h-0 flex-1 overflow-y-auto md:grid-cols-[1fr_260px]">
                <div class="relative grid min-h-[320px] place-items-center bg-[#101113] md:min-h-[520px]">
                    {{-- Intro / consent --}}
                    <div x-show="state === 'intro'" class="max-w-md px-6 py-10 text-center text-white">
                        <span class="mx-auto grid size-16 place-items-center rounded-full border border-[#b8914f]/50 text-[#e8d3a2]"><x-icon name="eye" class="size-7"/></span>
                        <h2 class="mt-5 text-3xl">See it on you</h2>
                        <p class="mt-3 text-sm leading-relaxed text-white/65">We use your camera only to place the frame on your face. Everything is processed <strong class="text-white">on your device</strong> — no video, photo or face data is uploaded or stored. The face-tracking model is downloaded from Google's MediaPipe servers.</p>
                        <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                            <button type="button" @click="startCamera()" class="btn-brand px-7 py-3.5"><x-icon name="eye" class="size-4"/> Start camera</button>
                            <label class="btn cursor-pointer border border-white/25 px-7 py-3.5 text-white hover:border-[#e8d3a2]">
                                <x-icon name="upload" class="size-4"/> Upload a photo
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="usePhoto($event.target.files[0]); $event.target.value = ''">
                            </label>
                        </div>
                        <p class="mt-5 text-[11px] text-white/40">Preview only — fit and size may differ slightly from the real frame.</p>
                    </div>
                    <video x-ref="video" playsinline muted class="hidden"></video>
                    <canvas x-ref="canvas" x-show="state !== 'intro'" class="max-h-[70vh] w-full object-contain"></canvas>
                    <div x-show="state === 'loading' || state === 'noface' || state === 'error'" class="absolute inset-x-4 bottom-4 rounded-2xl px-4 py-3 text-center text-sm"
                         :class="state === 'error' ? 'bg-sale text-white' : 'bg-white/90 text-ink'" role="status" aria-live="polite" x-text="message"></div>
                </div>

                <div class="flex flex-col gap-4 p-5">
                    <p class="label !mb-0">Choose a frame</p>
                    <div class="grid grid-cols-3 gap-2 md:grid-cols-2">
                        <template x-for="f in frames" :key="f.id">
                            <button type="button" @click="pick(f)" class="rounded-2xl border-2 bg-white p-1.5 transition" :class="frame && frame.id === f.id ? 'border-ink' : 'border-line hover:border-[#b8914f]'" :aria-pressed="frame && frame.id === f.id" :aria-label="'Try ' + f.name">
                                <img :src="f.thumb" alt="" class="aspect-[4/3] w-full object-contain">
                            </button>
                        </template>
                    </div>
                    <div x-show="frame" class="rounded-2xl bg-[#faf7f1] p-4 text-sm">
                        <p class="font-semibold" x-text="frame?.name"></p>
                        <p class="mt-1 font-bold" x-text="frame?.price"></p>
                        <a :href="frame?.url" class="btn-primary mt-3 w-full !py-2.5">View &amp; buy</a>
                    </div>
                    <div class="mt-auto grid grid-cols-2 gap-2" x-show="state === 'live' || state === 'noface' || state === 'photo'">
                        <button type="button" x-show="state !== 'photo'" @click="flip()" class="btn-outline !px-3 !py-2.5 text-xs">Switch camera</button>
                        <button type="button" @click="save()" class="btn-outline !px-3 !py-2.5 text-xs">Save photo</button>
                        <button type="button" @click="reset()" class="btn-ghost col-span-2 !py-2 text-xs">Start over</button>
                    </div>
                    <p class="text-[11px] leading-relaxed text-muted">Virtual Try-On is available for {{ $tryFrames->count() }} frames with front-view photos. More frames are added as new photos arrive.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
