<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    sendgrid: Object,
});
const form = useForm({
    provider: "sendgrid",
    key: null,
    ...props.sendgrid,
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
            label="Sendgrid Api Key"
            :error="form.errors.key"
            v-model="form.key"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full mt-4"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Sendgrid Settings</PrimaryButton
        >
    </div>
</template>
