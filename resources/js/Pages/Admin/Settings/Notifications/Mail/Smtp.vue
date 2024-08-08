<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import FormLabel from "@/Components/FormLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import RadioSelect from "@/Components/RadioSelect.vue";

const props = defineProps({
    smtp: Object,
});
const form = useForm({
    provider: "smtp",
    host: "mailpit",
    port: 1025,
    username: null,
    password: null,
    encryption: null,
    ...props.smtp,
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
            label="SMTP Host"
            :error="form.errors.host"
            v-model="form.host"
        />
        <FormInput
            label="SMTP Port"
            :error="form.errors.port"
            v-model="form.port"
        />
        <FormInput
            label="Username"
            :error="form.errors.username"
            v-model="form.username"
        />
        <FormInput
            label="Password"
            :error="form.errors.password"
            v-model="form.password"
        />

        <div>
            <FormLabel class="mb-2">Mail Encryption</FormLabel>
            <RadioSelect
                :options="[
                    { label: 'NONE', key: 'none', value: null },
                    { label: 'TLS', key: 'tls', value: 'tls' },
                    { label: 'SSL', key: 'ssl', value: 'ssl' },
                ]"
                v-model="form.encryption"
            />
            <p
                v-if="form.errors.encryption"
                class="text-red-500 text-xs font-semibold"
            >
                {{ form.errors.encryption }}
            </p>
        </div>
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full mt-4"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Smtp Settings</PrimaryButton
        >
    </div>
</template>
