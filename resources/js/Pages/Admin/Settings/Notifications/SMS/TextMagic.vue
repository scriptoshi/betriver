<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    textmagic: Object,
});
const form = useForm({
    provider: "textmagic",
    apiv2_key: null,
    username: null,
    ...props.textmagic,
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
            label="Textmagic Api V2 Key"
            :error="form.errors.apiv2_key"
            v-model="form.apiv2_key"
        />
        <FormInput
            label="Textmagic Username"
            :error="form.errors.username"
            v-model="form.username"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Textmagic Settings</PrimaryButton
        >
    </div>
</template>
