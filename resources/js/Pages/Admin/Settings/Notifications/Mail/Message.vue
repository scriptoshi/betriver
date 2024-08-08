<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import FormLabel from "@/Components/FormLabel.vue";
import FormTextArea from "@/Components/FormTextArea.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    title: String,
    notification: String,
    message: String,
    subject: String,
    salutation: String,
    sms: String,
});
const form = useForm({
    notification: props.notification,
    message: props.message,
    subject: props.subject,
    salutation: props.salutation,
    sms: props.sms,
});

const save = () => {
    form.post(window.route("admin.settings.messages.store"), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>
<template>
    <div class="grid gap-4 p-6 border dark:border-gray-600 rounded-sm">
        <h3 class="text-xl text-emerald-500 dark:text-emerald-400 mb-4">
            {{ title }}
        </h3>
        <FormInput
            label="Email Subject"
            :error="form.errors.subject"
            v-model="form.subject"
        /><FormInput
            label="Email Salutation"
            :error="form.errors.salutation"
            v-model="form.salutation"
        />
        <div>
            <FormLabel class="mb-2">Email Message</FormLabel>
            <FormTextArea v-model="form.message" :rows="2" />
            <p
                class="text-red-500 font-semibold text-xs mt-1"
                v-if="form.errors.message"
            >
                {{ form.errors.message }}
            </p>
        </div>
        <FormInput
            label="Message SMS Version"
            :error="form.errors.sms"
            v-model="form.message"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full mt-4"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Message</PrimaryButton
        >
    </div>
</template>
