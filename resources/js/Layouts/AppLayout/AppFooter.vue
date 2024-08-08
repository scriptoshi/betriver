<script setup>
import { computed } from "vue";

import { usePage } from "@inertiajs/vue3";
const appName = computed(() => usePage().props.appName);
</script>
<template>
    <footer
        class="footer flex flex-auto items-center h-16 max-h-16 px-4 sm:px-6 md:px-8 mt-10 bg-gray-200 dark:bg-gray-800"
    >
        <div class="flex items-center justify-between flex-auto w-full">
            <span
                >Copyright © {{ new Date().getFullYear() }}
                <span class="font-semibold">{{ appName }}</span> All rights
                reserved.</span
            >
            <div class="">
                <a class="text-gray" :href="route('terms')">{{
                    $t("Term & Conditions")
                }}</a
                ><span class="mx-2 text-muted"> | </span
                ><a class="text-gray" :href="route('privacy')">{{
                    $t("Privacy & Policy")
                }}</a>
            </div>
        </div>
    </footer>
</template>
