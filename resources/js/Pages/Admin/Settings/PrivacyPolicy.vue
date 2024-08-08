<script setup>
import { ref } from "vue";

import { router, useForm } from "@inertiajs/vue3";
import {
    HiRefresh,
    MdSettingsOutlined,
    RiAirplayFill,
} from "oh-vue-icons/icons";

import FormInput from "@/Components/FormInput.vue";
import FormLabel from "@/Components/FormLabel.vue";
import FormTextArea from "@/Components/FormTextArea.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const props = defineProps({
    pages: Object,
});

const form = useForm({
    privacy: null,
    terms: null,
    gdprNotice: null,
    gdprTerms: null,
    ...props.pages,
});
const saving = ref(1);
const save = (num) => {
    saving.value = num;
    form.post(window.route("admin.settings.privacy.store"));
};

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
                            Privacy and Terms
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Configure Privacy Policy and Terms of service
                    </p>
                </div>
            </div>
            <div class="grid gap-5 mt-8">
                <div class="card">
                    <div class="card-body p-6 grid gap-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <VueIcon
                                    :icon="RiAirplayFill"
                                    class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                />
                                <h3
                                    class="text-lg !text-sky-600 dark:!text-sky-400"
                                >
                                    Privacy Policy
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
                            <p>
                                The information will be rendered on the privacy
                                policy page. <br />
                            </p>
                            <ul class="space-y-1">
                                <li class="flex items-centre space-x-3">
                                    Use the shorthand to refer your generic
                                    appname
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
                        <div>
                            <FormLabel class="mb-4 text-xl"
                                >Privacy Policy.<br />
                                <small>
                                    <code
                                        class="dark:bg-emerald-500/20 bg-sky-500/20 px-1"
                                        >Markdown</code
                                    >
                                    syntax is supported
                                </small></FormLabel
                            >
                            <FormTextArea v-model="form.privacy" :rows="16" />
                            <p>
                                To preview the changes
                                <a
                                    target="_blank"
                                    class="text-sky-500 dark:hover:text-sky-300 hover:text-sky-700"
                                    href="/privacy"
                                    >GO HERE</a
                                >
                            </p>
                        </div>
                        <div class="py-4">
                            <FormLabel class="mb-4 text-xl"
                                >Terms of service. <br />
                                <small>
                                    <code
                                        class="dark:bg-emerald-500/20 bg-sky-500/20 px-1"
                                        >Markdown</code
                                    >
                                    syntax is supported
                                </small></FormLabel
                            >
                            <FormTextArea v-model="form.terms" :rows="16" />
                            <p>
                                To preview the changes
                                <a
                                    target="_blank"
                                    class="text-sky-500 dark:hover:text-sky-300 hover:text-sky-700"
                                    href="/terms"
                                    >GO HERE</a
                                >
                            </p>
                        </div>

                        <div class="py-4">
                            <h3 class="text-xl">GDPR NOTICE</h3>
                            <p>
                                These information will rendered on the GDPR
                                Modal. To disable the modal leave the title
                                empty.
                            </p>
                            <ul class="space-y-1">
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
                            label="GDPR Modal Title"
                            class="max-w-lg"
                            v-model="form.gdprNotice"
                        />
                        <div class="py-4">
                            <FormLabel class="mb-4 text-xl"
                                >GDRP Message.</FormLabel
                            >
                            <FormTextArea v-model="form.gdprTerms" :rows="8" />
                        </div>
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
                                Save Policies
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
