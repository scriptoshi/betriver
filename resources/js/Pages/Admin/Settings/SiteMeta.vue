<script setup>
import { ref, watch } from "vue";

import { router, useForm } from "@inertiajs/vue3";
import {
    HiRefresh,
    MdSettingsOutlined,
    RiCpuLine,
    RiVoiceRecognitionLine,
} from "oh-vue-icons/icons";

import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const props = defineProps({
    meta: Object,
});

const form = useForm({
    sitemapLeagues: null,
    sitemapGames: null,
    sitemapEnableMitemap: true,
    sitemapEnableMeta: true,
    homeTitle: null,
    homeKeywords: null,
    homeDescription: null,
    gamesTitle: null,
    gamesKeywords: null,
    gamesDescription: null,
    gameTitle: null,
    gameKeywords: null,
    gameDescription: null,
    ...props.meta,
});
const saving = ref(1);
const save = (num) => {
    saving.value = num;
    form.post(window.route("admin.settings.meta.store"));
};

watch(
    [() => form.sitemapEnableMitemap, () => form.sitemapEnableMeta],
    ([sitemapEnableMitemap, sitemapEnableMeta]) => {
        form.post(window.route("admin.settings.meta.store"));
    }
);
const reloading = ref(false);
const reload = () => {
    reloading.value = true;
    router.reload({ onFinish: () => (reloading.value = false) });
};
const deleteForm = useForm({});
const generateForm = useForm({});
const deletingSitemap = ref(false);
const deleteSitemap = () => {
    deleteForm.delete(window.route("admin.sitemap.destroy"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (deletingSitemap.value = true),
    });
};
const generateSitemap = () => {
    generateForm.post(window.route("admin.sitemap.generate"), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
<template>
    <Head title="Site settings" />
    <AdminLayout>
        <div class="container py-8">
            <div class="grid sm:grid-cols-2">
                <div>
                    <div class="flex items-center space-x-1">
                        <VueIcon
                            :icon="MdSettingsOutlined"
                            class="w-6 h-6 text-emerald-500"
                        />
                        <h1
                            class="text-2xl font-semibold text-gray-900 dark:text-gray-100"
                        >
                            Site Meta
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Configure Site SEO meta tags
                    </p>
                </div>
                <div class="flex justify-end items-center">
                    <div class="px-2 sm:px-5 text-center dark:border-gray-600">
                        <Switch v-model="form.sitemapEnableMeta">{{
                            $t("Meta Tags")
                        }}</Switch>
                        <p v-if="form.sitemapEnableMeta" class="text-gray-400">
                            Meta Active
                        </p>
                        <p v-else class="text-gray-400">Meta Disabled</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <Switch v-model="form.sitemapEnableMitemap">{{
                            $t("Site Map")
                        }}</Switch>
                        <p
                            v-if="form.sitemapEnableMitemap"
                            class="text-gray-400"
                        >
                            Site Map Active
                        </p>
                        <p v-else class="text-gray-400">Site Map Disabled</p>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-5 mt-8">
                <div class="card sm:col-span-2">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="RiCpuLine"
                                    class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Meta Tags
                                </h3>
                            </div>
                            <a
                                @click.prevent="reload"
                                v-tippy="$t('Reload')"
                                href="#"
                            >
                                <VueIcon
                                    :class="{ 'animate-spin': reloading }"
                                    :icon="HiRefresh"
                                />
                            </a>
                        </div>

                        <div class="pb-4">
                            <h3 class="text-xl">Universal / Home Page</h3>
                            <p>
                                These Meta information will be rendered on all
                                pages on the site except for games pages.
                            </p>
                            <ul class="space-y-1">
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:games_count</code
                                    >
                                    <p>The Total Number of games pending</p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:total_bets</code
                                    >
                                    <p>
                                        The Total Number of bets placed so far
                                    </p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:wins</code
                                    >
                                    <p>
                                        The Total amount of winnings distributed
                                        so far
                                    </p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:appname</code
                                    >
                                    <p>Site Settings App Name</p>
                                </li>
                            </ul>
                        </div>
                        <FormInput
                            label="Universal Meta Title"
                            class="max-w-lg"
                            v-model="form.homeTitle"
                        />
                        <FormInput
                            label="Universal Meta Description"
                            v-model="form.homeDescription"
                        />
                        <FormInput
                            label="Universal Meta Keywords"
                            v-model="form.homeKeywords"
                        />
                        <div class="py-4">
                            <h3 class="text-xl">Games Listing Page</h3>
                            <p>
                                These Meta information will be rendered on the
                                games listing pages
                            </p>
                            <ul class="space-y-1">
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:games_count</code
                                    >
                                    <p>The Total Number of games pending</p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:total_bets</code
                                    >
                                    <p>
                                        The Total Number of bets placed so far
                                    </p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:wins</code
                                    >
                                    <p>
                                        The Total amount of winnings distributed
                                        so far
                                    </p>
                                </li>
                            </ul>
                        </div>
                        <FormInput
                            label="Games Meta Title"
                            class="max-w-lg"
                            v-model="form.gamesTitle"
                        />
                        <FormInput
                            label="Games Meta Description"
                            v-model="form.gamesDescription"
                        />
                        <FormInput
                            label="Games Meta Keywords"
                            v-model="form.gamesKeywords"
                        />

                        <div class="py-4">
                            <h3 class="text-xl">Show Game Details Page</h3>
                            <p>
                                These Meta information will be rendered on the
                                game details pages
                            </p>
                            <ul class="space-y-1">
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:game</code
                                    >
                                    <p>The Name of the game</p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:time</code
                                    >
                                    <p>Game kickoff time</p>
                                </li>
                                <li class="flex items-centre space-x-3">
                                    <code
                                        class="dark:bg-gray-500/20 bg-sky-500/20 px-1"
                                        >:appname</code
                                    >
                                    <p>Site settings App Name</p>
                                </li>
                            </ul>
                        </div>
                        <FormInput
                            label="Show Game Meta Title"
                            class="max-w-lg"
                            v-model="form.gameTitle"
                        />
                        <FormInput
                            label="Show Game Meta Description"
                            v-model="form.gameDescription"
                        />
                        <FormInput
                            label="Show Game Meta Keywords"
                            v-model="form.gameKeywords"
                        />

                        <div class="flex justify-end">
                            <PrimaryButton
                                :disabled="form.processing && saving == 2"
                                @click="save(2)"
                                primary
                            >
                                <Loading
                                    class="mr-2 -ml-1"
                                    v-if="form.processing && saving == 2"
                                />
                                Save Meta Settings
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="RiVoiceRecognitionLine"
                                    class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Site Generation Settings
                                </h3>
                            </div>
                            <a
                                @click.prevent="reload"
                                v-tippy="$t('Reload')"
                                href="#"
                            >
                                <VueIcon
                                    :class="{ 'animate-spin': reloading }"
                                    :icon="HiRefresh"
                                />
                            </a>
                        </div>
                        <h3>About Sitemap</h3>
                        <p>
                            The sitemap will be generated once daily at 00:00
                            hours UTC
                        </p>
                        <FormInput
                            label="Number of Leagues to show in the sitemap"
                            v-model="form.sitemapLeagues"
                        />
                        <FormInput
                            label="Number of Games to show in the sitemap"
                            v-model="form.sitemapGames"
                        />

                        <PrimaryButton
                            :disabled="form.processing && saving == 1"
                            @click="save(1)"
                            primary
                        >
                            <Loading
                                class="mr-2 -ml-1"
                                v-if="form.processing && saving == 1"
                            />
                            Save Sitemap Settings
                        </PrimaryButton>
                        <PrimaryButton
                            @click="generateSitemap"
                            class="uppercase"
                            :disabled="generateForm.processing"
                            secondary
                        >
                            <Loading
                                class="mr-2 -ml-1"
                                v-if="generateForm.processing"
                            />
                            Generate Site map Now
                        </PrimaryButton>
                        <PrimaryButton
                            @click.prevent="deletingSitemap = true"
                            :disabled="deletingSitemap"
                            class="uppercase !text-red-500"
                            secondary
                        >
                            <Loading
                                class="mr-2 -ml-1"
                                v-if="deleteForm.processing"
                            />
                            Delete current sitemap
                        </PrimaryButton>
                    </div>
                    <ConfirmationModal
                        :show="deletingSitemap"
                        @close="deletingSitemap = null"
                    >
                        <template #title>
                            {{
                                $t("Are you sure about deleting your sitemap ?")
                            }}
                        </template>

                        <template #content>
                            <p>
                                {{
                                    $t(
                                        "This Action will remove the sitemap and will not generate a fresh one. You can always regenerate your sitemap if need be."
                                    )
                                }}
                            </p>
                            <p>
                                {{
                                    $t(
                                        "Its Recommended to generate a new sitemap instead"
                                    )
                                }}
                            </p>
                        </template>

                        <template #footer>
                            <PrimaryButton
                                primary
                                class="uppercase text-xs font-semibold"
                                @click="deletingSitemap = false"
                            >
                                {{ $t("Cancel") }}
                            </PrimaryButton>

                            <PrimaryButton
                                error
                                class="ml-2 uppercase text-xs font-semibold"
                                @click="deleteSitemap"
                                :class="{
                                    'opacity-25': deleteForm.processing,
                                }"
                                :disabled="deleteForm.processing"
                            >
                                {{ $t("Delete") }}
                            </PrimaryButton>
                        </template>
                    </ConfirmationModal>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
