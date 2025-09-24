<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    // kept for compatibility with previous behavior (no-op for this page)
}
</script>

<template>
    <Head title="Welcome" />

    <div class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
        <div class="relative flex h-screen items-center justify-center">
            <div class="container mx-auto px-6">
                <header class="flex items-center justify-between py-8">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-md bg-gray-800 dark:bg-gray-100/5 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white dark:text-gray-100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M3 6h18M3 12h18M3 18h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium tracking-wide">Contacts Manager</span>
                    </div>

                    <nav class="flex items-center gap-3">
                        <Link v-if="canLogin && $page.props.auth.user" :href="route('dashboard')" class="text-sm text-gray-700 dark:text-gray-200">Dashboard</Link>
                        <template v-else>
                            <Link v-if="canLogin" :href="route('login')" class="text-sm text-gray-700 hover:underline dark:text-gray-200">Log in</Link>
                            <Link v-if="canRegister" :href="route('register')" class="ml-3 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100">Register</Link>
                        </template>
                    </nav>
                </header>

                <main class="grid items-center gap-12 lg:grid-cols-2">
                    <section class="max-w-2xl">
                        <h1 class="text-4xl font-extrabold leading-tight text-gray-900 dark:text-gray-100">Keep your contacts organized — fast, private, and effortless.</h1>

                        <p class="mt-4 text-gray-600 dark:text-gray-300">A simple contact manager built for speed and clarity. Import, organize, and reach out to the people who matter — without the clutter.</p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-500">Get started</Link>
                            <Link v-if="canLogin" :href="route('login')" class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200">Log in</Link>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="flex flex-col items-start gap-3">
                                <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c2.485 0 4.5-2.015 4.5-4.5S14.485 2 12 2 7.5 4.015 7.5 6.5 9.515 11 12 11zM4.5 20.5a7.5 7.5 0 0 1 15 0"/></svg>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Privacy first</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300">Your contacts stay private and secure.</p>
                            </div>

                            <div class="flex flex-col items-start gap-3">
                                <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.5 20.5h9a2 2 0 0 0 2-2V7.5a2 2 0 0 0-2-2h-9a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2z"/></svg>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Organize easily</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300">Groups, tags and notes to keep things tidy.</p>
                            </div>

                            <div class="flex flex-col items-start gap-3">
                                <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2v6m0 8v6M4 8h16M4 16h16"/></svg>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Integrations</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-300">Import from CSV, sync with tools you use.</p>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200">Get started in 3 easy steps</h4>
                            <ol class="mt-3 flex flex-col gap-3">
                                <li class="flex items-start gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full border border-gray-300 text-sm text-gray-800">1</div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Add or import contacts</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Quick CSV import or manual entry.</p>
                                    </div>
                                </li>

                                <li class="flex items-start gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full border border-gray-300 text-sm text-gray-800">2</div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Organize with groups</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Create groups, add tags and notes.</p>
                                    </div>
                                </li>

                                <li class="flex items-start gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full border border-gray-300 text-sm text-gray-800">3</div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Reach out</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Export, email, or call directly from the app.</p>
                                    </div>
                                </li>
                            </ol>
                        </div>
                    </section>

                    <aside class="order-first -mb-6 lg:order-last lg:mb-0">
                        <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                            <div class="h-40 w-full rounded-md bg-gray-100 dark:bg-gray-700" aria-hidden="true"></div>
                            <div class="mt-4">
                                <p class="text-sm text-gray-700 dark:text-gray-200">A clean space to preview your contact list. Minimal distractions, maximum clarity.</p>
                            </div>
                        </div>
                    </aside>
                </main>

                <footer class="mt-12 text-center text-xs text-gray-500 dark:text-gray-400">Laravel v{{ laravelVersion }} — PHP v{{ phpVersion }}</footer>
            </div>
        </div>
    </div>
</template>

