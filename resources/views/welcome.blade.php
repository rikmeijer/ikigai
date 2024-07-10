<x-guest-layout>
    <x-slot name="header">
        <div class="flex lg:justify-center lg:col-start-2">
            <a href="/" wire:navigate>
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>
        @if (Route::has('login'))
            <livewire:welcome.navigation />
        @endif
    </x-slot>

    <main class="mt-6">
        <div class="grid gap-6 lg:gap-8">
            <div
                id="docs-card"
                class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
            >
                <div class="relative flex items-center gap-6 lg:items-end">
                    <div id="docs-card-content" class="flex items-start gap-6 lg:flex-col">
                        <div class="pt-3 sm:pt-5 lg:pt-0">
                            <h2 class="text-xl font-semibold text-black dark:text-white">{{config("app.name")}}</h2>
                            <p class="mt-4 text-sm/relaxed">{{config("app.tagline")}}</p>
                            <h3 class="text-xl font-semibold text-black dark:text-white">What is your dream?</h3>
                        </div>
                    </div>

                </div>
                </div>

                </div>
    </main>       
</x-guest-layout>
