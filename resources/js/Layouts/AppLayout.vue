<script setup>
import { computed, ref, watch } from "vue";

import { usePage } from "@inertiajs/vue3";
import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";

import AlertMessages from "@/Layouts/AlertMessages.vue";
import AppFooter from "@/Layouts/AppLayout/AppFooter.vue";
import AppHeader from "@/Layouts/AppLayout/AppHeader.vue";
import Meta from "@/Layouts/AppLayout/Meta.vue";
import Sidebar from "@/Layouts/AppLayout/Sidebar.vue";
import VerifyEmailModal from "@/Pages/Auth/VerifyEmailModal.vue";

const AuthCheck = computed(() => usePage().props.AuthCheck);
const expanded = ref(true);

defineProps({
    title: String,
});
const draw = (val) => (expanded.value = val);
const breakpoints = useBreakpoints(breakpointsTailwind);
const isSm = breakpoints.smaller("md");
watch(
    isSm,
    (isSm) => {
        expanded.value = !isSm;
    },
    { immediate: true }
);
</script>
<template>
    <Meta :title="title" />
    <div class="relative">
        <AlertMessages />
        <!-- disabled to save memory. hogs cpu during dev-->
        <!--TopAd /-->
        <div @scroll="onScroll" class="flex flex-auto min-w-0">
            <Sidebar @draw="draw" :expanded="expanded" />
            <div
                class="flex flex-col flex-auto min-h-screen min-w-0 relative w-full bg-gray-100 dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700"
            >
                <AppHeader @toggle="expanded = !expanded" />
                <div class="h-full flex flex-auto flex-col justify-between">
                    <div
                        class="flex items-center font-semibold mx-6 px-4 py-2 my-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50/50 dark:bg-gray-800/50 dark:text-yellow-300 dark:border-yellow-800"
                        role="alert"
                        v-if="route().current('home')"
                    >
                        <svg
                            class="flex-shrink-0 inline w-4 h-4 me-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
                            />
                        </svg>
                        <span class="sr-only">Info</span>
                        <div>
                            <span class="font-bold">We are on Testnet!</span>
                            Betn is currently on testnet phase deployed to
                            sepolia only. Binance Mainnet is comming on 1st
                            June. Check Telegram for more.
                        </div>
                    </div>
                    <slot />
                    <AppFooter />
                </div>
            </div>
        </div>

        <VerifyEmailModal v-if="AuthCheck" />
    </div>
</template>
