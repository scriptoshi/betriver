<script setup>
import { computed, ref, watch } from "vue";

import { router, useForm } from "@inertiajs/vue3";
import {
    HiRefresh,
    MdSettingsOutlined,
    MdTextsmsOutlined,
    RiMailAddLine,
    RiMailSettingsLine,
} from "oh-vue-icons/icons";

import CollapseTransition from "@/Components/CollapseTransition.vue";
import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RadioSelect from "@/Components/RadioSelect.vue";
import Switch from "@/Components/Switch.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import MailGunSetting from "@/Pages/Admin/Settings/Notifications/Mail/MailGun.vue";
import MailjetSetting from "@/Pages/Admin/Settings/Notifications/Mail/Mailjet.vue";
import PostmarkSetting from "@/Pages/Admin/Settings/Notifications/Mail/Postmark.vue";
import SendgridSetting from "@/Pages/Admin/Settings/Notifications/Mail/Sendgrid.vue";
import SmtpSetting from "@/Pages/Admin/Settings/Notifications/Mail/Smtp.vue";
import NotificationMessages from "@/Pages/Admin/Settings/Notifications/Messages.vue";
import ClickatellSettings from "@/Pages/Admin/Settings/Notifications/SMS/Clickatell.vue";
import MessageBirdSettings from "@/Pages/Admin/Settings/Notifications/SMS/MessageBird.vue";
import SmspohSettings from "@/Pages/Admin/Settings/Notifications/SMS/Smspoh.vue";
import TextMagicSettings from "@/Pages/Admin/Settings/Notifications/SMS/TextMagic.vue";
import TouchSmsSettings from "@/Pages/Admin/Settings/Notifications/SMS/TouchSms.vue";
import VonageSettings from "@/Pages/Admin/Settings/Notifications/SMS/Vonage.vue";
const props = defineProps({
    smspoh: Object,
    touchsms: Object,
    clickatell: Object,
    textmagic: Object,
    messagebird: Object,
    vonage: Object,
    notifications: Object,
    mail: Object,
    sms: Object,
    smtp: Object,
    mailgun: Object,
    postmark: Object,
    aws_ses: Object,
    smsDrivers: Array,
    mailDrivers: Array,
});

const form = useForm({
    enable_sms: true,
    enable_email: true,
    mailer: "smtp",
    sms: "vonage",
    from_address: null,
    from_name: null,
    ...props.notifications,
});
const mailer = computed(() =>
    props.mailDrivers.find((d) => d.value === form.mailer)
);
const sms = computed(() => props.smsDrivers.find((d) => d.value === form.sms));
const saving = ref(1);
const save = (num) => {
    saving.value = num;
    form.post(window.route("admin.settings.notifications.store"));
};

watch(
    [() => form.enable_email, () => form.enable_sms],
    ([enableEmail, enableSms]) => {
        form.post(window.route("admin.settings.notifications.store"));
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
                            Site Notifications
                        </h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Configure Site Notifications
                    </p>
                </div>
                <div class="flex justify-end items-center">
                    <div class="px-2 sm:px-5 text-center dark:border-gray-600">
                        <Switch v-model="form.enable_email">{{
                            $t("Mail")
                        }}</Switch>
                        <p v-if="form.enable_email" class="text-gray-400">
                            Mail Active
                        </p>
                        <p v-else class="text-gray-400">Mail Disabled</p>
                    </div>
                    <div
                        class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600"
                    >
                        <Switch v-model="form.enable_sms">{{
                            $t("SMS")
                        }}</Switch>
                        <p v-if="form.enable_sms" class="text-gray-400">
                            SMS Active
                        </p>
                        <p v-else class="text-gray-400">SMS Disabled</p>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-3 gap-5 mt-8">
                <div class="sm:col-span-2">
                    <div class="card">
                        <div class="card-body p-6 grid gap-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <VueIcon
                                        :icon="RiMailAddLine"
                                        class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                    />
                                    <h3
                                        class="text-lg !text-sky-600 dark:!text-sky-400"
                                    >
                                        Email Configuration
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
                                <h3 class="text-lg">
                                    Site Mailer Service provider
                                </h3>

                                <p>
                                    To use your environment (.env ) values, set
                                    the mailer to .ENV CONFIG
                                </p>
                            </div>
                            <FormInput
                                label="Universal Email from Address"
                                class="max-w-lg"
                                v-model="form.from_address"
                            />
                            <FormInput
                                label="Universal Email from Name"
                                v-model="form.from_name"
                            />
                            <div
                                class="p-4 border dark:border-gray-600 rounded-md"
                            >
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg">Mail Driver</h3>

                                    <span
                                        v-if="form.enable_email"
                                        class="bg-green-100 uppercase font-semibold text-green-800 text-xs px-2.5 py-0.5 rounded dark:bg-gray-900 dark:text-green-400 border border-green-400"
                                        >Active</span
                                    >
                                    <span
                                        v-else
                                        class="bg-red-100 uppercase font-semibold text-red-800 text-xs px-2.5 py-0.5 rounded dark:bg-gray-900 dark:text-red-400 border border-red-400"
                                        >Disabled</span
                                    >
                                </div>
                                <p class="mb-3">
                                    Remember to configure driver after saving
                                </p>
                                <RadioSelect
                                    v-model="form.mailer"
                                    :options="mailDrivers"
                                    :grid="3"
                                />
                            </div>
                            <div
                                class="p-4 border dark:border-gray-600 rounded-md"
                            >
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg">SMS Driver</h3>

                                    <span
                                        v-if="form.enable_sms"
                                        class="bg-green-100 uppercase font-semibold text-green-800 text-xs px-2.5 py-0.5 rounded dark:bg-gray-900 dark:text-green-400 border border-green-400"
                                        >Active</span
                                    >
                                    <span
                                        v-else
                                        class="bg-red-100 uppercase font-semibold text-red-800 text-xs px-2.5 py-0.5 rounded dark:bg-gray-900 dark:text-red-400 border border-red-400"
                                        >Disabled</span
                                    >
                                </div>
                                <p class="mb-3">
                                    Remember to configure driver after saving
                                </p>
                                <RadioSelect
                                    v-model="form.sms"
                                    :options="smsDrivers"
                                    :grid="3"
                                />
                            </div>
                            <div class="flex justify-end mt-4">
                                <PrimaryButton
                                    :disabled="form.processing && saving == 2"
                                    @click="save(2)"
                                    primary
                                >
                                    <Loading
                                        class="mr-2 -ml-1"
                                        v-if="form.processing && saving == 2"
                                    />
                                    Save Mail Config
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                    <NotificationMessages
                        class="mt-4"
                        :mail="mail"
                        :sms="sms"
                    />
                </div>
                <div>
                    <div class="card">
                        <div class="card-body p-6 grid gap-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <VueIcon
                                        :icon="RiMailSettingsLine"
                                        class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                    />
                                    <h3
                                        class="text-lg !text-sky-600 dark:!text-sky-400"
                                    >
                                        {{ mailer.label }} Settings
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
                            <CollapseTransition>
                                <SmtpSetting
                                    v-show="form.mailer === 'smtp'"
                                    :smtp="smtp"
                                />
                            </CollapseTransition>

                            <CollapseTransition>
                                <MailGunSetting
                                    v-show="form.mailer === 'mailgun'"
                                    :mailgun="mailgun"
                                />
                            </CollapseTransition>

                            <CollapseTransition>
                                <MailjetSetting
                                    v-show="form.mailer === 'mailjet'"
                                    :mailjet="mailjet"
                                />
                            </CollapseTransition>

                            <CollapseTransition>
                                <PostmarkSetting
                                    v-show="form.mailer === 'postmark'"
                                    :postmark="postmark"
                                />
                            </CollapseTransition>

                            <CollapseTransition
                                ><SendgridSetting
                                    v-show="form.mailer === 'sendgrid'"
                                    :sendgrid="sendgrid"
                                />
                            </CollapseTransition>
                        </div>
                    </div>
                    <div class="card mt-6">
                        <div class="card-body p-6 grid gap-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <VueIcon
                                        :icon="MdTextsmsOutlined"
                                        class="mr-2 h-6 w-6 text-emerald-600 dark:text-emerald-400"
                                    />
                                    <h3
                                        class="text-lg !text-sky-600 dark:!text-sky-400"
                                    >
                                        {{ sms.label }} Settings
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
                            <CollapseTransition>
                                <SmspohSettings
                                    v-show="form.sms === 'smspoh'"
                                    :smspoh="smspoh"
                            /></CollapseTransition>
                            <CollapseTransition
                                ><MessageBirdSettings
                                    v-show="form.sms === 'messagebird'"
                                    :messagebird="messagebird"
                            /></CollapseTransition>
                            <CollapseTransition
                                ><ClickatellSettings
                                    v-show="form.sms === 'clickatell'"
                                    :clickatell="clickatell"
                            /></CollapseTransition>
                            <CollapseTransition
                                ><TouchSmsSettings
                                    v-show="form.sms === 'touchsms'"
                                    :touchsms="touchsms"
                            /></CollapseTransition>
                            <CollapseTransition>
                                <VonageSettings
                                    v-show="form.sms === 'vonage'"
                                    :vonage="vonage"
                            /></CollapseTransition>
                            <CollapseTransition>
                                <TextMagicSettings
                                    v-show="form.sms === 'textmagic'"
                                    :textmagic="textmagic"
                            /></CollapseTransition>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
