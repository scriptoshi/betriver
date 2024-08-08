<script setup>
import { Link } from "@inertiajs/vue3";

import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>
<template>
    <div class="min-h-screen flex items-center overflow-hidden w-full">
        <div class="flex w-full min-h-screen bg-white dark:bg-gray-900">
            <img
                id="background"
                class="absolute -left-20 top-0 max-w-[877px]"
                src="./background.svg"
            />
            <div
                class="basis-1/2 hidden relative bg-transparent md:flex items-center justify-center"
            >
                <div
                    class="bg-amber-100 relative dark:bg-amber-900 p-8 rounded-lg shadow-md max-w-md"
                >
                    <div class="flex gap-4 items-center mb-4">
                        <div
                            class="w-10 h-10 bg-gray-200/20 rounded-full flex items-center justify-center text-white text-2xl font-bold"
                        >
                            <ApplicationLogo class="w-5 h-5" />
                        </div>
                        <div
                            class="text-4xl font-bold text-amber-900 dark:text-amber-100"
                        >
                            {{ $page.props.appName }}
                        </div>
                    </div>
                    <h2
                        class="text-4xl font-bold mb-2 text-gray-800 dark:text-white"
                    >
                        Time to swim in winnings
                    </h2>
                    <p class="text-gray-600 text-lg dark:text-gray-300 mb-6">
                        You might never win everything. But you will win more...
                    </p>
                    <div class="flex items-center justify-center">
                        <button
                            class="bg-amber-200 dark:bg-amber-700 rounded-full p-4"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-amber-600 dark:text-amber-300"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div
                class="basis-full md:basis-1/2 flex items-center justify-center"
            >
                <div class="">
                    <Link href="/">
                        <ApplicationLogo
                            class="w-20 mb-4 h-20 fill-current text-gray-500"
                        />
                    </Link>
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
