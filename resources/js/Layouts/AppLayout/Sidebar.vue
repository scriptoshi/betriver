<script setup>
import { computed } from "vue";

import { XMarkIcon } from "@heroicons/vue/24/outline";
import { BookOpenIcon, HomeIcon } from "@heroicons/vue/24/solid";
import { Link, usePage } from "@inertiajs/vue3";
import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";
import { uid } from "uid";
import { useI18n } from "vue-i18n";

import SiteLogo from "@/Components/ApplicationLogo.vue";
import CollapseTransition from "@/Components/CollapseTransition.vue";
import Baseball from "@/Layouts/AppLayout/Icons/Baseball.vue";
import BasketBall from "@/Layouts/AppLayout/Icons/BasketBall.vue";
import HandBall from "@/Layouts/AppLayout/Icons/HandBall.vue";
import Hockey from "@/Layouts/AppLayout/Icons/Hockey.vue";
import NbaIcon from "@/Layouts/AppLayout/Icons/Nba.vue";
import NflIcon from "@/Layouts/AppLayout/Icons/Nfl.vue";
import Rugby from "@/Layouts/AppLayout/Icons/Rugby.vue";
import Soccer from "@/Layouts/AppLayout/Icons/Soccer.vue";
import VolleyBall from "@/Layouts/AppLayout/Icons/VolleyBall.vue";
import Lang from "@/Layouts/AppLayout/Lang.vue";
import MenuItem from "@/Layouts/AppLayout/MenuItem.vue";
import MiniMenuItem from "@/Layouts/AppLayout/MiniMenuItem.vue";
import Discord from "@/Social/Discord.vue";
import Telegram from "@/Social/Telegram.vue";
import Twitter from "@/Social/Twitter.vue";

const config = computed(() => usePage().props.config);
const { t } = useI18n();
const AuthCheck = computed(() => usePage().props.AuthCheck);
const twitterUrl = computed(() => usePage().props?.config?.twitterUrl);
const telegramUrl = computed(() => usePage().props?.config?.telegramUrl);
const discordUrl = computed(() => usePage().props?.config?.discordUrl);
const soccers = computed(() => usePage().props?.soccers ?? {});
const menus = computed(() => [
    ...(AuthCheck.value
        ? [
              {
                  name: t("Home"),
                  route: "home",
                  active: window.route().current("home"),
                  url: window.route("home"),
                  icon: HomeIcon,
              },
          ]
        : [
              {
                  name: t("Home"),
                  route: "home",
                  active: window.route().current("home"),
                  url: window.route("home"),
                  icon: HomeIcon,
              },
          ]),

    {
        name: t("Soccer"),
        route: null,
        active:
            window.route().current("sports.index", { sport: "soccer" }) ||
            window.route().current("games.*", { sport: "soccer" }),
        url: null,
        icon: Soccer,

        submenu: [
            {
                id: uid(),
                name: "LIVE",
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: "live",
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: "live",
                }),
            },
            {
                id: uid(),
                name: "TODAY",
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: "today",
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: "today",
                }),
            },
            {
                id: uid(),
                name: "TOMMORROW",
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: "tommorrow",
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: "tommorrow",
                }),
            },
            {
                id: uid(),
                name: "THIS WEEK",
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: "this-week",
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: "this-week",
                }),
            },
            {
                id: uid(),
                name: "ENDED",
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: "ended",
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: "ended",
                }),
            },

            ...Object.keys(soccers.value).map((region) => ({
                id: uid(),
                name: region.toUpperCase(),
                active: window.route().current("sports.index", {
                    sport: "soccer",
                    country: region.toLowerCase(),
                }),
                url: window.route("sports.index", {
                    sport: "soccer",
                    country: region.toLowerCase(),
                }),
            })),
        ],
    },
    {
        name: t("NFL"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "nfl" }),
        url: window.route("sports.index", { sport: "nfl" }),
        icon: NflIcon,
    },
    {
        name: t("NBA"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "nba" }),
        url: window.route("sports.index", { sport: "nba" }),
        icon: NbaIcon,
    },
    {
        name: t("Rugby"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "rugby" }),
        url: window.route("sports.index", { sport: "rugby" }),
        icon: Rugby,
    },
    {
        name: t("Basketball"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "basketball" }),
        url: window.route("sports.index", { sport: "basketball" }),
        icon: BasketBall,
    },

    {
        name: t("Baseball"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "baseball" }),
        url: window.route("sports.index", { sport: "baseball" }),
        icon: Baseball,
    },
    {
        name: t("Handball"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "handball" }),
        url: window.route("sports.index", { sport: "handball" }),
        icon: HandBall,
    },
    {
        name: t("Volleyball"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "volleyball" }),
        url: window.route("sports.index", { sport: "volleyball" }),
        icon: VolleyBall,
    },
    {
        name: t("Hockey"),
        route: "sports.index",
        active: window.route().current("sports.index", { sport: "hockey" }),
        url: window.route("sports.index", { sport: "hockey" }),
        icon: Hockey,
    },
    /** {
		name: t("Formular One"),
		route: "sports.index",
		active: window.route().current("sports.index", {sport: "formular-one"}),
		url: window.route("sports.index", {sport: "formular-one"}),
		icon: FormularOne,
	},**/
    {
        name: "line",
        url: null,
        icon: null,
    },
    {
        name: "docs",
        url: "https://docs.betn.io",
        icon: BookOpenIcon,
    },
    {
        name: t("Telegram"),
        url: telegramUrl.value,
        icon: Telegram,
    },
    {
        name: t("Twitter"),
        url: twitterUrl.value,
        icon: Twitter,
    },
    {
        name: t("Discord"),
        url: discordUrl.value,
        icon: Discord,
    },
]);
const breakpoints = useBreakpoints(breakpointsTailwind);
const isLg = breakpoints.greaterOrEqual("sm");
defineProps({
    expanded: Boolean,
});
const emit = defineEmits(["draw"]);
const draw = (val) => emit("draw", val);

function currentRoute(route = []) {
    if (!route) return false;
    if (typeof route === "string") return window.route().current(route);
    return route.filter((rt) => window.route().current(rt)).length > 0;
}
</script>
<template>
    <div
        v-if="isLg"
        :class="{ 'min-w-[200px]': expanded }"
        class="h-screen sticky z-1 top-0 flex-auto flex-col bg-gray-200/50 dark:bg-gray-900 flex-shrink-0 transition-all duration-300 ease-in-out"
    >
        <Link
            href="/"
            :class="expanded ? 'px-6' : 'pl-6'"
            class="py-4 w-auto flex items-center"
        >
            <SiteLogo class="w-10 h-auto mr-3" />
            <h3 class="font-extralight" v-if="expanded">BETN</h3>
        </Link>
        <CollapseTransition dimension="width">
            <div
                v-show="expanded"
                class="h-[calc(100vh-4rem)] group max-w-[202px] scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-800 scrollbar-thumb-rounded-lg"
            >
                <nav class="pb-8 mb-8">
                    <div>
                        <ul>
                            <MenuItem
                                v-for="menu in menus"
                                :key="menu.id"
                                :menu="menu"
                                :active="
                                    menu.route
                                        ? currentRoute(menu.route)
                                        : false
                                "
                            />
                        </ul>
                    </div>
                </nav>
            </div>
        </CollapseTransition>
        <nav v-show="!expanded" class="pb-8 relative">
            <div class="flex relative flex-col">
                <MiniMenuItem
                    v-for="menu in menus"
                    :key="menu.id"
                    :menu="menu"
                />
            </div>
        </nav>
        <Lang :expanded="expanded" />
    </div>

    <!-- drawer component -->
    <template v-else>
        <div
            :class="expanded ? 'transform-none' : '-translate-x-full'"
            class="fixed z-40 h-screen overflow-y-auto bg-white w-80 dark:bg-gray-800 transition-transform left-0 top-0"
            tabindex="-1"
        >
            <div
                class="flex items-center justify-between p-4 border-gray-300 dark:border-gray-600 border-b"
            >
                <div class="flex items-center">
                    <SiteLogo class="w-10 h-auto mr-3" />
                    <h5
                        class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400"
                    >
                        {{ config.appName }}
                    </h5>
                </div>
                <button
                    type="button"
                    @click="draw(false)"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                >
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>
            <div class="py-8">
                <ul class="space-y-2">
                    <MenuItem
                        v-for="menu in menus"
                        :key="menu.id"
                        :menu="menu"
                    />
                </ul>
            </div>
            <Lang :expanded="expanded" />
        </div>
        <div
            v-if="expanded"
            class="bg-gray-900/70 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30"
        ></div>
    </template>
</template>
