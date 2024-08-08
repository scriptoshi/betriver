<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    clickatell: Object,
});
const form = useForm({
    provider: "clickatell",
    apikey: null,
    ...props.clickatell,
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
            label="Clickatell Api Key"
            :error="form.errors.apikey"
            v-model="form.apikey"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Clickatell Settings</PrimaryButton
        >
    </div>
</template>
