<script setup>
import { computed, onMounted, ref, watch } from "vue";

import { router as Inertia, usePage } from "@inertiajs/vue3";
import { onClickOutside } from "@vueuse/core";
import { useI18n } from "vue-i18n";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import Cn from "@/flags/cn.svg";
import De from "@/flags/de.svg";
import Us from "@/flags/us.svg";

defineProps({
    expanded: Boolean,
});
const langs = [
    {
        name: "English",
        id: "en",
        icon: Us,
    },
    {
        name: "Deutsche",
        id: "de",
        icon: De,
    },
    {
        name: "Chinese",
        id: "cn",
        icon: Cn,
    },
];
const selected = ref(langs[0]);
const previous = ref();
const lang = computed(() => usePage().props.locale ?? "en");
const active = computed(() => selected.value?.id);
const { locale } = useI18n({ useScope: "global" });
watch(active, (active) => {
    if (active && active !== lang.value) {
        Inertia.post(
            window.route("lang"),
            { lang: active },
            {
                preserveState: false,
                onFinish: () => (locale.value = active),
            }
        );
    }
});
onMounted(() => {
    selected.value = langs.find((l) => l.id === lang.value);
    previous.value = selected.value;
});
const select = (lang) => {
    previous.value = selected.value;
    selected.value = selected.value ? null : lang;
};
const outside = ref();
onClickOutside(outside, () => {
    if (selected.value === null) {
        selected.value = previous.value;
    }
});
</script>
<template>
    <div
        class="pt-2 bg-gray-200 dark:bg-gray-900 sticky bottom-0"
        ref="outside"
    >
        <template v-for="lang in langs" :key="lang.id">
            <CollapseTransition>
                <a
                    class="py-2 px-6 w-auto flex items-center"
                    href="#"
                    @click.prevent="select(lang)"
                    v-show="!selected || selected.id === lang.id"
                >
                    <img :src="lang.icon" class="rounded-full mr-2 w-6 h-6" />
                    <span v-if="expanded">{{ lang.name }}</span>
                </a>
            </CollapseTransition>
        </template>
    </div>
</template>
