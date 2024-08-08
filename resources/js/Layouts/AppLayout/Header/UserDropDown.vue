<script setup>
import { computed, ref } from "vue";

import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import {
    CogIcon,
    GiftIcon,
    ArrowLeftEndOnRectangleIcon as LogoutIcon,
    Squares2X2Icon,
    UserPlusIcon as UserAddIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";
import { Link as InertiaLink, usePage } from "@inertiajs/vue3";
import { useDisconnect } from "use-wagmi";
import { useI18n } from "vue-i18n";

import Loading from "@/Components/Loading.vue";
import { logOut } from "@/Wagmi/hooks/authentication";
const { disconnect } = useDisconnect();
const signOut = () => {
    disconnect();
    logOut();
};
const user = computed(() => usePage().props.user);
const avatar = computed(
    () =>
        user.value?.avatar?.src ??
        user.value?.profile_photo_url ??
        user.value?.gravatar
);
const { t } = useI18n();
const userNavigation = [
    {
        name: t("Your Profile"),
        href: window.route("profile.edit", user.value.username),
        icon: UserIcon,
        active: window.route().current("profile.edit"),
    },

    {
        name: t("Linked Accounts"),
        href: window.route("accounts.index"),
        icon: CogIcon,
        active: window.route().current("account.*"),
    },

    {
        name: t("Referral Earnings"),
        href: window.route("user.referrals"),
        icon: UserAddIcon,
        active: false,
    },
    {
        name: t("Your Farms"),
        href: "#",
        icon: Squares2X2Icon,
        active: false,
    },
    {
        name: t("Documentation"),
        href: "https://docs.betn.io",
        icon: GiftIcon,
        active: false,
    },
];
const load = ref(false);
const logout = async () => {
    load.value = true;
    await signOut();
};
</script>
<template>
    <Menu as="div" class="ml-3 relative dropdown">
        <div>
            <MenuButton
                class="bg-white dark:bg-gray-800 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
                <div class="relative">
                    <img class="w-8 h-auto rounded-full" :src="avatar" alt="" />
                    <span
                        class="hidden sm:block top-0 left-7 absolute w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"
                    ></span>
                </div>
            </MenuButton>
        </div>
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <MenuItems
                class="aside bottom place-left origin-top-right absolute right-0 mt-5 w-56 rounded-md shadow-lg pt-4 bg-white dark:bg-gray-800 ring-1 ring-gray-300 dark:ring-gray-600 ring-opacity-5 focus:outline-none"
            >
                <div class="tip" style="left: calc(100% - 20px)"></div>

                <MenuItem
                    v-for="item in userNavigation"
                    :key="item.name"
                    v-slot="{ active }"
                >
                    <InertiaLink
                        :href="item.href"
                        :class="[
                            active
                                ? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-400'
                                : 'text-gray-700 dark:text-gray-300',
                            'group flex items-center px-6 py-2 text-base hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-emerald-600/20 dark:hover:text-emerald-300',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            class="mr-3 h-5 w-5 text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
                            aria-hidden="true"
                        />
                        {{ item.name }}
                    </InertiaLink>
                </MenuItem>
                <MenuItem as="div">
                    <hr
                        class="border-t border-gray-200 dark:border-gray-600 mt-2"
                    />
                    <InertiaLink
                        href="#"
                        @click="logout"
                        class="group text-gray-700 dark:text-gray-300 flex items-center px-6 py-4 text-base hover:bg-gray-200 dark:hover:bg-gray-600"
                    >
                        <Loading
                            v-if="load"
                            class="!text-emerald-500 !mr-3 !h-5 !w-5"
                        />
                        <LogoutIcon
                            v-else
                            class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300"
                            aria-hidden="true"
                        />
                        {{ $t("Sign out") }}
                    </InertiaLink>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>
<style>
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown .target-wrap {
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    display: inline-block;
}
.dropdown .aside.dropdown-menu {
    background-color: #fff;
    position: absolute;
    z-index: 1001;
    display: block;
    border: 1px solid #dadbdd;
    border-radius: 3px;
    -webkit-box-shadow: 0 0 5px 1px rgba(0, 0, 0, 0.05);
    box-shadow: 0 0 5px 1px rgba(0, 0, 0, 0.05);
}
.dropdown .aside .tip,
.dropdown .aside .tip:after {
    content: "";
    position: absolute;
    display: block;
    width: 0;
    height: 0;
    border-color: transparent;
    border-style: solid;
    border-width: 10px;
}
.dropdown .aside.bottom .tip,
.dropdown .aside.bottom .tip:after,
.dropdown .aside.top .tip,
.dropdown .aside.top .tip:after {
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
}
.dropdown .aside.bottom.place-center,
.dropdown .aside.bottom.place-center .tip,
.dropdown .aside.bottom.place-center .tip:after,
.dropdown .aside.top.place-center,
.dropdown .aside.top.place-center .tip,
.dropdown .aside.top.place-center .tip:after {
    left: 50%;
}
.dropdown .aside.bottom.place-left,
.dropdown .aside.top.place-left {
    right: 0;
}
.dropdown .aside.left.place-center,
.dropdown .aside.left.place-center .tip,
.dropdown .aside.left.place-center .tip:after,
.dropdown .aside.right.place-center,
.dropdown .aside.right.place-center .tip,
.dropdown .aside.right.place-center .tip:after {
    top: 50%;
}
.dropdown .aside.left .tip,
.dropdown .aside.left .tip:after,
.dropdown .aside.right .tip,
.dropdown .aside.right .tip:after {
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
}
.dropdown .aside.left.place-top,
.dropdown .aside.right.place-top {
    top: 100%;
}
.dropdown .aside.left.place-bottom,
.dropdown .aside.right.place-bottom {
    top: 0;
}
.dropdown .aside.bottom .tip,
.dropdown .aside.bottom .tip:after {
    border-top-width: 0;
    border-bottom-color: rgba(0, 0, 0, 0.25);
    top: -11px;
}
.dark .dropdown .aside.bottom .tip,
.dark .dropdown .aside.bottom .tip:after {
    border-bottom-color: #57534e;
}
.dropdown .aside.bottom .tip:after {
    top: 1px;
    border-bottom-color: #fff;
}
.dark .dropdown .aside.bottom .tip:after {
    top: 1px;
    border-bottom-color: #292524;
}
.dropdown .aside.bottom.place-center {
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
}
.dropdown .aside.top {
    top: 0;
    -webkit-transform: translateY(-100%);
    transform: translateY(-100%);
}
.dropdown .aside.top .tip,
.dropdown .aside.top .tip:after {
    border-bottom-width: 0;
    border-top-color: rgba(0, 0, 0, 0.25);
    top: 100%;
}
.dropdown .aside.top .tip:after {
    border-top-color: #fff;
    top: -11px;
}
.dropdown .aside.top.place-center {
    -webkit-transform: translate(-50%, -100%);
    transform: translate(-50%, -100%);
}
.dropdown .aside.left {
    -webkit-transform: translateX(-100%);
    transform: translateX(-100%);
}
.dropdown .aside.left .tip,
.dropdown .aside.left .tip:after {
    border-right-width: 0;
    border-left-color: rgba(0, 0, 0, 0.25);
    left: 100%;
}
.dropdown .aside.left .tip:after {
    border-left-color: #fff;
    left: -11px;
}
.dropdown .aside.left.place-top {
    -webkit-transform: translate(-100%, -100%);
    transform: translate(-100%, -100%);
}
.dropdown .aside.left.place-center {
    -webkit-transform: translate(-100%, -50%);
    transform: translate(-100%, -50%);
}
.dropdown .aside.right {
    left: 100%;
}
.dropdown .aside.right .tip,
.dropdown .aside.right .tip:after {
    left: -11px;
    border-left-width: 0;
    border-right-color: rgba(0, 0, 0, 0.25);
}
.dropdown .aside.right .tip:after {
    left: 1px;
    border-right-color: #fff;
}
.dropdown .aside.right.place-center {
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
}
.dropdown .aside.right.place-top {
    -webkit-transform: translateY(-100%);
    transform: translateY(-100%);
}

.dexeye-nav .dropdown-menu .aside {
    position: absolute;
    z-index: 100;
    top: 50%;
    margin-top: 33px;
}

.dexeye-nav .dropdown-menu .dropdown-content {
    min-width: 180px;
    padding: 5px 15px;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
}
.dexeye-nav .dropdown-content li {
    padding: 5px 0;
    white-space: nowrap;
}

@media only screen and (min-width: 1160px) {
    .dexeye-nav .dropdown-content li.show-link {
        display: none;
    }
}
.dexeye-nav .dropdown-content .divider {
    border-top: 1px solid #dadbdd;
    padding: 0;
    margin: 5px 0;
    font-size: 0;
}
.dexeye-nav .vertical-divider {
    border-left: 1px solid #dadbdd;
}

.dexeye-nav ul li .nav-label,
.dexeye-nav ul li .nav-link {
    white-space: nowrap;
    font-weight: 600;
    text-decoration: none;
    color: #74767e;
    padding: 0;
}
.dexeye-nav ul li .nav-label.nav-link-green,
.dexeye-nav ul li .nav-label:hover,
.dexeye-nav ul li .nav-link.nav-link-green,
.dexeye-nav ul li .nav-link:hover {
    color: #1dbf73;
}

.dexeye-nav ul li .nav-label.nav-link-green:hover,
.dexeye-nav ul li .nav-link.nav-link-green:hover {
    color: #3fca89;
}
.dexeye-nav ul li .nav-label.nav-link-azure,
.dexeye-nav ul li .nav-link.nav-link-azure {
    color: #02c2a9;
}

.dexeye-nav ul li .nav-label.nav-link-azure:hover,
.dexeye-nav ul li .nav-link.nav-link-azure:hover {
    color: #28ccb7;
}
.dexeye-nav ul li ul li .nav-link {
    font-weight: 400;
    display: block;
}
.dexeye-nav ul li ul li .nav-link:hover {
    color: #1dbf73;
}
</style>
