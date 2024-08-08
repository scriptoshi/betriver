<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    touchsms: Object,
});
const form = useForm({
    provider: "touchsms",
    token_id: null,
    access_token: null,
    default_sender: null,
    ...props.touchsms,
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
            label="Touchsms Token ID"
            :error="form.errors.token_id"
            v-model="form.token_id"
        />
        <FormInput
            label="Touchsms Access Token"
            :error="form.errors.access_token"
            v-model="form.access_token"
        />
        <FormInput
            label="Touchsms SMS Sender"
            :error="form.errors.default_sender"
            v-model="form.default_sender"
            help="The sms from field"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Touchsms Settings</PrimaryButton
        >
    </div>
</template>
