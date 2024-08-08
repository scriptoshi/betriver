<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    messagebird: Object,
});
const form = useForm({
    provider: "messagebird",
    access_key: null,
    originator: null,
    ...props.messagebird,
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
            label="Messagebird Access Key"
            :error="form.errors.access_key"
            v-model="form.access_key"
        />

        <FormInput
            label="Messagebird SMS Originator"
            :error="form.errors.originator"
            v-model="form.originator"
            help="The sms from field"
        />
        <PrimaryButton
            :disabled="form.processing"
            @click="save"
            class="w-full"
            primary
        >
            <Loading class="mr-2 -ml-1" v-if="form.processing" />
            Save Messagebird Settings</PrimaryButton
        >
    </div>
</template>
