<script setup>
import { computed, ref } from "vue";

import { usePage } from "@inertiajs/vue3";

import Logo from "@/Components/ApplicationLogo.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AnimatedMobileIcon from "@/Layouts/AdminLayout/AnimatedMobileButton.vue";
import DarkSwitch from "@/Layouts/AdminLayout/DarkSwitch.vue";
import {
    HiCog,
    HiUserCircle,
    LaPowerOffSolid,
    MdAdminpanelsettingsOutlined,
    RiShieldKeyholeLine,
} from "oh-vue-icons/icons";

const showUserMenu = ref(false);

const logoUrl = computed(() => usePage().props.appLogo);
const appName = computed(() => usePage().props.appName);
const props = defineProps({
    modelValue: Boolean,
});
const emit = defineEmits(["update:modelValue"]);
const toggle = () => emit("update:modelValue", !props.modelValue);
</script>
<template>
    <nav
        class="bg-sky-500 dark:bg-blue-500 border-b border-gray-300 dark:border-gray-600 fixed z-30 w-full"
    >
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button
                        @click="toggle"
                        class="lg:hidden mr-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded"
                    >
                        <AnimatedMobileIcon :open="modelValue" />
                    </button>
                    <a
                        href="/admin"
                        class="text-2xl font-bold flex items-center lg:ml-2.5"
                    >
                        <img
                            v-if="logoUrl"
                            :src="logoUrl"
                            class="h-6 mr-2"
                            alt="Windster Logo"
                        />
                        <Logo v-else class="h-8 mr-2" />
                        <span
                            class="self-center hidden md:flex text-white whitespace-nowrap"
                            >{{ appName }} Admin</span
                        >
                    </a>
                </div>
                <div class="flex space-y-0 flex-row items-center space-x-6">
                    <button
                        @click="toggle"
                        class="btn h-8 w-8 rounded-full p-0 bg-slate-300/20 hover:bg-slate-300/40 focus:bg-slate-300/40 active:bg-slate-300/30 dark:bg-navy-300/20 dark:hover:bg-navy-300/40 dark:focus:bg-navy-300/40 dark:active:bg-navy-300/30"
                    >
                        <VueIcon
                            :icon="MdAdminpanelsettingsOutlined"
                            class="w-7 h-7 text-white"
                        />
                    </button>
                    <button
                        @click="toggle"
                        class="btn h-8 w-8 rounded-full p-0 bg-slate-300/20 hover:bg-slate-300/40 focus:bg-slate-300/40 active:bg-slate-300/30 dark:bg-navy-300/20 dark:hover:bg-navy-300/40 dark:focus:bg-navy-300/40 dark:active:bg-navy-300/30"
                    >
                        <VueIcon :icon="HiCog" class="w-7 h-7 text-white" />
                    </button>
                    <DarkSwitch />
                    <div class="relative">
                        <div>
                            <button
                                @click="showUserMenu = !showUserMenu"
                                class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                id="user-menu"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                <img
                                    class="h-7 w-7 rounded-full"
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                    alt=""
                                />
                            </button>
                        </div>
                        <transition
                            enter-active-class="transition-opacity duration-300 ease-linear"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition-opacity duration-300 ease-linear"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <div
                                v-if="showUserMenu"
                                class="origin-top-right absolute right-0 mt-4 w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="user-menu"
                            >
                                <div
                                    class="bg-sky-500 rounded-t-md font-semibold text-white text-center p-3"
                                >
                                    <VueIcon :icon="HiCog" />
                                    Admin Menu
                                </div>
                                <a
                                    href="#"
                                    class="font-inter flex items-center font-semibold px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    role="menuitem"
                                >
                                    <VueIcon
                                        class="w-5 h-5 mr-2"
                                        :icon="HiUserCircle"
                                    /><span>Profile Profile</span></a
                                >
                                <a
                                    href="#"
                                    class="font-inter flex items-center font-semibold px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    role="menuitem"
                                >
                                    <VueIcon
                                        class="w-5 h-5 mr-2"
                                        :icon="RiShieldKeyholeLine"
                                    /><span>Authentication</span></a
                                ><a
                                    href="#"
                                    class="font-inter flex items-center font-semibold px-3 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                    role="menuitem"
                                >
                                    <VueIcon
                                        class="w-5 h-5 mr-2"
                                        :icon="LaPowerOffSolid"
                                    /><span>Sign Out</span></a
                                >
                            </div></transition
                        >
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
