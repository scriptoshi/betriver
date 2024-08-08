import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { createI18n } from "vue-i18n";
import VueTippy from "vue-tippy";
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';

import "tippy.js/dist/tippy.css";
import "tippy.js/themes/light.css";
import "v-calendar/dist/style.css";
// eslint-disable-next-line import/order
import messages from "./vue-i18n-locales.generated.js";

const appName = import.meta.env.VITE_APP_NAME || 'BetRiver';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const i18n = createI18n({
            locale: props.initialPage.props.locale,
            fallbackLocale: "en", // set fallback locale
            messages, // set locale messages,
            legacy: false,
        });
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(VueTippy)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
