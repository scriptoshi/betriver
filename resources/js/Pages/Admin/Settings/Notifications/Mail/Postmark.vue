<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    postmark: Object,
});
const form = useForm({
    provider: "postmark",
    token: null,
    ...props.postmark,
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
            label="Postmark Api Token"
            :error="form.errors.token"
            v-model="form.token"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full mt-4"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Postmark Settings</PrimaryButton
        >
    </div>
</template>
