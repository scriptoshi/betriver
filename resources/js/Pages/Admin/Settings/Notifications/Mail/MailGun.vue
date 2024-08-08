<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    mailgun: Object,
});
const form = useForm({
    provider: "mailgun",
    secret: null,
    domain: null,
    ...props.mailgun,
});

const save = () => {
    form.post(window.route("admin.settings.mail.store"), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
<template>
    <div class="grid gap-4">
        <FormInput
            label="Mailgun Secret Key"
            :error="form.errors.secret"
            v-model="form.secret"
        />
        <FormInput
            label="Mailgun Domain"
            :error="form.errors.domain"
            v-model="form.domain"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full mt-4"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Mailgun Settings</PrimaryButton
        >
    </div>
</template>
