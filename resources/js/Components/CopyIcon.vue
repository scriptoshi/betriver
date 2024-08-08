<script setup>
import { useClipboard } from "@vueuse/core";
import {
    MdContentcopyRound,
    MdLibraryaddcheckTwotone,
} from "oh-vue-icons/icons";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import VueIcon from "@/Components/VueIcon.vue";
defineProps({
    text: {
        type: [String, Number],
        required: true,
    },
    after: Boolean,
});
const { copy, copied } = useClipboard({
    copiedDuring: 2000,
});
</script>

<template>
    <a class="relative w-6 h-6" @click.prevent="copy(text)" href="#">
        <CollapseTransition>
            <VueIcon
                v-show="!copied"
                v-bind="$attrs"
                class="text-gray-600 absolute hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                :icon="MdContentcopyRound"
            />
        </CollapseTransition>
        <CollapseTransition>
            <VueIcon
                v-show="copied"
                v-bind="$attrs"
                class="text-green-500 absolute"
                :icon="MdLibraryaddcheckTwotone"
            />
        </CollapseTransition>
    </a>
</template>
