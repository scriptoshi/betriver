<script setup>
import { computed, ref } from "vue";

import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { MdErrorTwotone } from "oh-vue-icons/icons";

import Loading from "@/Components/Loading.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import VueIcon from "@/Components/VueIcon.vue";

const status = computed(() => usePage().props.status);
const verified = computed(() => usePage().props.verified);
const AuthCheck = computed(() => usePage().props.AuthCheck);
const busy = ref(false);
const reload = () => {
    busy.value = true;
    router.reload({
        only: ["verified"],
        preserveScroll: true,
        preserveState: false,
        onFinish() {
            busy.value = false;
        },
    });
};

const form = useForm({});

const resend = () => {
    form.post(window.route("verification.send"));
};

const verificationLinkSent = computed(
    () => status.value === "verification-link-sent"
);
</script>
<template>
    <Modal
        :show="!verified && AuthCheck && !route().current('profile.*')"
        max-width="lg"
        :closeable="false"
    >
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-6">
                <div class="mt-8">
                    <div
                        class="flex mb-4 w-full items-center justify-start space-x-4"
                    >
                        <VueIcon
                            class="text-gray-400 w-20 h-20 dark:text-gray-200"
                            :icon="MdErrorTwotone"
                        />
                        <h3>Email Verification</h3>
                    </div>

                    <div
                        class="mb-4 text-sm font-semibold text-gray-600 dark:text-gray-400"
                    >
                        Before continuing, could your verify your email address
                        by clicking on the link we just emailed to you? If you
                        didn't receive the email, we will gladly send you
                        another.
                    </div>

                    <div
                        v-if="verificationLinkSent"
                        class="mb-4 font-medium text-sm text-emerald-600 dark:text-emerald-300"
                    >
                        A new verification link has been sent to the email
                        address you provided in your profile settings.
                    </div>
                    <div class="my-4 w-full flex items-center space-x-4">
                        <Link
                            :href="route('profile.edit')"
                            class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-200"
                        >
                            Edit Profile</Link
                        >

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 hover:text-gray-900 ml-2 transition-colors duration-200"
                        >
                            Log Out
                        </Link>
                    </div>
                </div>
                <hr />
                <div class="pt-4">
                    <PrimaryButton
                        class="!py-2"
                        primary
                        @click.prevent="resend"
                    >
                        <Loading
                            class="-ml-1 mr-2 inline-flex"
                            v-if="form.processing"
                        />
                        {{ $t("Resend") }}
                    </PrimaryButton>
                    <PrimaryButton
                        class="!py-2"
                        secondary
                        @click.prevent="reload"
                    >
                        <Loading
                            class="-ml-1 mr-2 !text-gray-500 inline-flex"
                            v-if="busy"
                        />{{ $t("All Done !") }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
