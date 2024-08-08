<script setup>
import { ref, watch } from "vue";

import { router, useForm } from "@inertiajs/vue3";
import {
    HiRefresh,
    MdSettingsOutlined,
    SiFacebook,
    SiGithub,
    SiGoogle,
} from "oh-vue-icons/icons";

import CopyIcon from "@/Components/CopyIcon.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const props = defineProps({
    social: Object,
});
const facebookRedirect = window.route("connections.callback", {
    provider: "facebook",
});
const githubRedirect = window.route("connections.callback", {
    provider: "github",
});
const form = useForm({
    enableGithub: true,
    githubClientId: null,
    githubClientSecret: null,
    enableGoogle: true,
    googleClientId: null,
    googleClientSecret: null,
    googleProjectId: null,
    enableFacebook: true,
    facebookClientId: null,
    facebookClientSecret: null,
    ...props.social,
    facebookRedirect,
    githubRedirect,
});
const saving = ref(1);
const save = (num) => {
    saving.value = num;
    form.post(window.route("admin.settings.social.store"));
};

watch(
    [
        () => form.enableGoogle,
        () => form.enableGithub,
        () => form.enableFacebook,
    ],
    ([enableGoogle, enableGithub, enableFacebook]) => {
        form.post(window.route("admin.settings.social.store"));
    }
);
const reloading = ref(false);
const reload = () => {
    reloading.value = true;
    router.reload({ onFinish: () => (reloading.value = false) });
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
                            Site Settings
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Configure Site and User interface settings
                    </p>
                </div>
                <div class="flex justify-end items-center">
                    <div class="px-2 sm:px-5 text-center dark:border-gray-600">
                        <Switch v-model="form.enableGithub">{{
                            $t("Github")
                        }}</Switch>
                        <p v-if="form.enableGithub" class="text-gray-400">
                            Github Active
                        </p>
                        <p v-else class="text-gray-400">Github Disabled</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <Switch v-model="form.enableGoogle">{{
                            $t("Google")
                        }}</Switch>
                        <p v-if="form.enableGithub" class="text-gray-400">
                            Google Active
                        </p>
                        <p v-else class="text-gray-400">Google Disabled</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <Switch v-model="form.enableFacebook">{{
                            $t("Facebook")
                        }}</Switch>
                        <p v-if="form.enableFacebook" class="text-gray-400">
                            Facebook Active
                        </p>
                        <p v-else class="text-gray-400">Facebook Disabled</p>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-5 mt-8">
                <div class="card">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="SiGoogle"
                                    class="mr-2 h-6 w-6 text-red-600 dark:text-red-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Google One Tap Settings
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
                        <FormInput
                            label="Google ClienId"
                            v-model="form.googleClientId"
                        />
                        <FormInput
                            label="Google Client Secret"
                            v-model="form.googleClientSecret"
                        />
                        <FormInput
                            label="Google Project ID"
                            v-model="form.googleProjectId"
                        />
                        <div class="flex justify-end">
                            <PrimaryButton @click="save(1)" primary>
                                <Loading
                                    class="mr-2 -ml-1"
                                    v-if="form.processing && saving == 1"
                                />
                                Save Google One Tap Settings
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="SiGithub"
                                    class="mr-2 h-6 w-6 text-sky-600 dark:text-sky-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Github Login Settings
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
                        <FormInput
                            label="Github ClienId"
                            v-model="form.githubClientId"
                        />
                        <FormInput
                            label="Github Client Secret"
                            v-model="form.githubClientSecret"
                        />
                        <FormInput
                            label="Github Redirect Url"
                            v-model="form.githubRedirect"
                            disabled
                        >
                            <template #trail>
                                <CopyIcon :text="form.githubRedirect" />
                            </template>
                        </FormInput>
                        <div class="flex justify-end">
                            <PrimaryButton @click="save(2)" primary>
                                <Loading
                                    class="mr-2 -ml-1"
                                    v-if="form.processing && saving == 2"
                                />
                                Save Github Settings
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="SiFacebook"
                                    class="mr-2 h-6 w-6 text-sky-600 dark:text-sky-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Facebook Login Settings
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
                        <FormInput
                            label="Facebook ClienId"
                            v-model="form.facebookClientId"
                        />
                        <FormInput
                            label="Facebook Client Secret"
                            v-model="form.facebookClientSecret"
                        />
                        <FormInput
                            label="Facebook Redirect Url"
                            v-model="form.facebookRedirect"
                            disabled
                        >
                            <template #trail>
                                <CopyIcon :text="form.facebookRedirect" />
                            </template>
                        </FormInput>
                        <div class="flex justify-end">
                            <PrimaryButton @click="save(3)" primary>
                                <Loading
                                    class="mr-2 -ml-1"
                                    v-if="form.processing && saving == 3"
                                />
                                Save Facebook Settings
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
