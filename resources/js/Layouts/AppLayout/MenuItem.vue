<script setup>
import { onMounted, ref } from "vue";

import { Link } from "@inertiajs/vue3";
import { HiChevronRight } from "oh-vue-icons/icons";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import VueIcon from "@/Components/VueIcon.vue";
const props = defineProps({
    menu: Object,
    active: Boolean,
});
const isOpen = ref(false);
onMounted(() => (isOpen.value = !!props.menu.active));
</script>
<template>
    <div>
        <div class="py-3" v-if="menu.name === 'line'">
            <hr class="border-gray-300 dark:border-gray-700" />
        </div>
        <template v-else>
            <a
                v-if="!!menu.url && !menu.route"
                :href="menu.url"
                target="_blank"
                :class="
                    menu.active
                        ? 'text-emerald-500 dark:text-emerald-400'
                        : ' text-gray-900 dark:text-gray-200'
                "
                class="items-center text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none mb-2 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-200/50 dark:hover:bg-gray-700/50"
            >
                <span class="flex items-center">
                    <component
                        class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400"
                        :is="menu.icon"
                    />
                    <div class="whitespace-nowrap">{{ menu.name }}</div>
                </span>
            </a>
            <Link
                v-else-if="!!menu.url"
                :href="menu.url"
                :class="
                    menu.active
                        ? 'text-emerald-500 dark:text-emerald-400'
                        : ' text-gray-900 dark:text-gray-200'
                "
                class="items-center text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none mb-2 hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-200/50 dark:hover:bg-gray-700/50"
            >
                <span class="flex items-center">
                    <component
                        class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400"
                        :is="menu.icon"
                    />
                    <div class="whitespace-nowrap">{{ menu.name }}</div>
                </span>
            </Link>
            <div
                v-else
                @click.prevent="isOpen = !isOpen"
                :class="[
                    menu.active
                        ? 'text-emerald-500 dark:text-emerald-400'
                        : ' text-gray-900 dark:text-gray-200',
                    { 'bg-gray-300 dark:bg-gray-700': isOpen },
                ]"
                class="items-center text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-200/50 dark:hover:bg-gray-700/50"
            >
                <span class="flex items-center">
                    <component
                        class="w-5 h-5 mr-2 text-emerald-600 dark:text-emerald-400"
                        :is="menu.icon"
                    />
                    <div class="whitespace-nowrap">{{ menu.name }}</div>
                </span>
                <span v-if="menu.submenu" class="text-lg mt-1">
                    <VueIcon
                        :icon="HiChevronRight"
                        :class="isOpen ? 'rotate-90' : ''"
                        class="w-4 h-4 transition-transform duration-300"
                    />
                </span>
            </div>
            <CollapseTransition>
                <ul v-show="isOpen" class="bg-gray-200 dark:bg-gray-800">
                    <div
                        v-for="sub in menu.submenu"
                        :key="sub.id"
                        :class="
                            sub.active
                                ? 'text-emerald-500 dark:text-emerald-400 bg-emerald-200/40 dark:bg-emerald-700/20 border-l-4 border-emerald-500'
                                : ' text-gray-600 dark:text-gray-400'
                        "
                        class="items-center transition-colors duration-300 h-8 text-md gap-x-2 cursor-pointer flex font-semibold pl-10 pr-6 whitespace-nowrap w-full hover:text-emerald-500 dark:hover:text-emerald-400 hover:bg-gray-200/50 dark:hover:bg-gray-700/50"
                    >
                        <Link
                            class="h-full w-full flex items-center"
                            :href="sub.url"
                            ><span>{{ sub.name }}</span></Link
                        >
                    </div>
                </ul>
            </CollapseTransition>
        </template>
    </div>
</template>
