<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    smspoh: Object,
});
const form = useForm({
    provider: "smspoh",
    endpoint: "https://smspoh.com/api/v2/send",
    token: null,
    sender: null,
    ...props.smspoh,
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
            label="Endpoint"
            :error="form.errors.endpoint"
            v-model="form.endpoint"
        />
        <FormInput
            label="Api Token"
            :error="form.errors.token"
            v-model="form.token"
        />
        <FormInput
            label="SMS Sender"
            :error="form.errors.sender"
            v-model="form.sender"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save SMSPo Settings</PrimaryButton
        >
    </div>
</template>
