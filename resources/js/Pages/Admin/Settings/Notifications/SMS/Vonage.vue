<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    vonage: Object,
});
const form = useForm({
    provider: "vonage",
    api_key: null,
    api_secret: null,
    sms_sender: null,
    ...props.vonage,
});

const save = () => {
    form.post(window.route("admin.settings.sms.store"), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
<template>
    <div class="grid gap-4">
        <FormInput
            label="Vonage Api Key"
            :error="form.errors.api_key"
            v-model="form.api_key"
        />
        <FormInput
            label="Vonage Api Secret"
            :error="form.errors.api_secret"
            v-model="form.api_secret"
        />
        <FormInput
            label="Vonage SMS Sender"
            :error="form.errors.sms_sender"
            v-model="form.sms_sender"
            help="The sms from field"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Vonage Settings</PrimaryButton
        >
    </div>
</template>
