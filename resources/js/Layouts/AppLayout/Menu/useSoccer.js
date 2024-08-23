import { computed, reactive, shallowReactive } from "vue";

import { BookOpenIcon, HomeIcon, StarIcon } from "@heroicons/vue/24/solid";
import { usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

import Baseball from "@/Layouts/AppLayout/Icons/Baseball.vue";
import Basketball from "@/Layouts/AppLayout/Icons/BasketBall.vue";
import AFL from "@/Layouts/AppLayout/Icons/CompassIcon.vue";
import Racing from "@/Layouts/AppLayout/Icons/FormularOne.vue";
import Handball from "@/Layouts/AppLayout/Icons/HandBall.vue";
import Hockey from "@/Layouts/AppLayout/Icons/Hockey.vue";
import LiveIcon from "@/Layouts/AppLayout/Icons/LiveIcon.vue";
import MMA from "@/Layouts/AppLayout/Icons/Mma.vue";
import NbaIcon from "@/Layouts/AppLayout/Icons/Nba.vue";
import NflIcon from "@/Layouts/AppLayout/Icons/Nfl.vue";
import Rugby from "@/Layouts/AppLayout/Icons/Rugby.vue";
import Soccer from "@/Layouts/AppLayout/Icons/Soccer.vue";
import Volleyball from "@/Layouts/AppLayout/Icons/VolleyBall.vue";
import Discord from "@/Social/Discord.vue";
import Telegram from "@/Social/Telegram.vue";
import Twitter from "@/Social/Twitter.vue";

const logos = {
    Baseball,
    Basketball,
    Handball,
    Hockey,
    NBA: NbaIcon,
    NFL: NflIcon,
    Rugby,
    AFL,
    MMA,
    Football: Soccer,
    Volleyball,
    Discord,
    Telegram,
    Twitter,
    Racing
};
const toTitleCase = (str) => {
    if (str.length === 3) return str.toUpperCase();
    return str
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};
export const makeMenu = ({ route, sport, region = null, country = null, count = 0, name = null }) => {
    const key = [route, sport, region, country].filter(Boolean).join('.');
    return {
        key,
        id: key,
        name: name ?? (region ? toTitleCase(region) : toTitleCase(sport)),
        route,
        params: { sport, region, country },
        icon: logos[toTitleCase(sport)] ?? null,
        ...count ? { count } : {},
        type: 'link',
        star: false
    };
};

/**
 * create menu objects from backend menu output
 */
export const useMenu = () => {
    const menuList = computed(() => usePage().props.menus ?? []);
    const twitterUrl = computed(() => usePage().props?.config?.twitterUrl);
    const telegramUrl = computed(() => usePage().props?.config?.telegramUrl);
    const discordUrl = computed(() => usePage().props?.config?.discordUrl);
    const liveCount = computed(() => usePage().props?.liveCount);
    const { t } = useI18n();
    const starrableMenus = reactive({
        home: {
            id: 'home',
            name: t("Home"),
            route: "home",
            icon: HomeIcon,
            type: 'link'
        },
        watchlist: {
            route: "sports.watchlist", params: {}, icon: StarIcon, name: 'Watchlist', star: false, type: 'link'
        },
        inplay: {
            route: "sports.inplay", params: {}, icon: LiveIcon, name: 'In Play', star: false, count: liveCount.value, type: 'link'
        },
    });
    const menus = menuList.value.map(menu => {
        const mnu = makeMenu(menu);
        const baseParams = { route: menu.route, sport: menu.sport };
        const submenu = [];
        if (menu.submenu.live > 0) submenu.push(makeMenu({ ...baseParams, region: 'live', count: menu.submenu.live }));
        if (menu.submenu.today > 0) submenu.push(makeMenu({ ...baseParams, region: 'today', count: menu.submenu.today }));
        if (menu.submenu.tomorrow > 0) submenu.push(makeMenu({ ...baseParams, region: 'tomorrow', count: menu.submenu.tomorrow }));
        if (menu.submenu.this_week > 0) submenu.push(makeMenu({ ...baseParams, region: 'this_week', count: menu.submenu.this_week }));
        if (menu.submenu.ended > 0) submenu.push(makeMenu({ ...baseParams, region: 'ended', count: menu.submenu.ended }));
        if (Object.keys(menu.submenu.leagues).length > 0) {
            if (menu.sport === 'football') {
                Object.keys(menu.submenu.leagues).forEach(ctry => {
                    const name = menu.submenu.leagues[ctry][0].country.name;
                    const deepMenu = [];
                    const footballMenu = makeMenu({ ...baseParams, region: 'region', country: ctry, name, });
                    menu.submenu.leagues[ctry].forEach(league => {
                        const menuItem = makeMenu({ ...baseParams, region: league.slug, name: league.name, count: league.count_games });
                        menuItem.star = true;
                        starrableMenus[menuItem.key] = menuItem;
                        deepMenu.push(menuItem);
                    });
                    footballMenu.deepMenu = deepMenu;
                    submenu.push(footballMenu);
                });
            } else {
                Object.values(menu.submenu.leagues).forEach(league => {
                    const menuItem = makeMenu({ ...baseParams, region: league.slug, name: league.name, count: league.count_games });
                    menuItem.star = true;
                    starrableMenus[menuItem.key] = menuItem;
                    submenu.push(menuItem);
                });
            }
        }
        mnu.star = true;
        starrableMenus[mnu.key] = mnu;
        if (submenu.length > 0) {
            mnu.submenu = submenu;
            mnu.type = 'menu';
        }
        return mnu;
    });
    const fullMenu = shallowReactive([
        ...menus,
        {
            name: "line",
            url: null,
            icon: null,
            type: 'line'
        },
        {
            name: "docs",
            url: "https://docs.betn.io",
            icon: BookOpenIcon,
            type: 'external'
        },
        {
            name: t("Telegram"),
            url: telegramUrl.value,
            icon: Telegram,
            type: 'external'
        },
        {
            name: t("Twitter"),
            url: twitterUrl.value,
            icon: Twitter,
            type: 'external'
        },
        {
            name: t("Discord"),
            url: discordUrl.value,
            icon: Discord,
            type: 'external'
        },
    ]);

    return { starrableMenus, fullMenu };

};

export const useStarableMenus = () => {
    const menuList = computed(() => usePage().props.menus ?? []);
    const menus = {};
    menuList.value.forEach(menu => {
        const mnu = makeMenu(menu);
        const baseParams = { route: menu.route, sport: menu.sport };
        const submenu = [];
        if (menu.submenu.live > 0) submenu.push(makeMenu({ ...baseParams, region: 'live', count: menu.submenu.live }));
        if (menu.submenu.today > 0) submenu.push(makeMenu({ ...baseParams, region: 'today', count: menu.submenu.today }));
        if (menu.submenu.tomorrow > 0) submenu.push(makeMenu({ ...baseParams, region: 'tomorrow', count: menu.submenu.tomorrow }));
        if (menu.submenu.this_week > 0) submenu.push(makeMenu({ ...baseParams, region: 'this_week', count: menu.submenu.this_week }));
        if (menu.submenu.ended > 0) submenu.push(makeMenu({ ...baseParams, region: 'ended', count: menu.submenu.ended }));
        if (Object.keys(menu.submenu.leagues).length > 0) {
            if (menu.sport === 'football') {
                Object.keys(menu.submenu.leagues).forEach(ctry => {
                    const name = menu.submenu.leagues[ctry][0].country.name;
                    const deepMenu = [];
                    const footballMenu = makeMenu({ ...baseParams, region: 'region', country: ctry, name, });
                    menu.submenu.leagues[ctry].forEach(league => {
                        deepMenu.push(makeMenu({ ...baseParams, region: league.slug, name: league.name, count: league.count_games }));
                    });
                    footballMenu.deepMenu = deepMenu;
                    submenu.push(footballMenu);
                });
            } else {
                Object.values(menu.submenu.leagues).forEach(league => {
                    submenu.push(makeMenu({ ...baseParams, region: league.slug, name: league.name, count: league.count_games }));
                });
            }
        }
        if (submenu.length > 0) {
            mnu.submenu = submenu;
            mnu.type = 'menu';
        }
        menus[mnu.key] = mnu;
    });

};