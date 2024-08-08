<script setup>
import { useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import Loading from "@/Components/Loading.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Switch from "@/Components/Switch.vue";

const props = defineProps({
    gateway: String,
    config: Object,
});
const form = useForm({
    gateway: props.gateway,
    ...props.config,
});

const save = () => {
    form.post(window.route("admin.settings.payments.store"), {
        preserveScroll: true,
        preserveState: true,
    });
};
const settings = Object.keys(props.config);
function titleCase(str) {
    return str
        .split("_")
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(" ");
}
</script>
<template>
    <div class="grid gap-4 p-6 border dark:border-gray-600 rounded-sm">
        <h3
            class="text-xl flex items-center text-emerald-500 dark:text-emerald-400 mb-4"
        >
            <img :src="config.logo" class="w-6 h-6 mr-6" />
            {{ titleCase(gateway) }} Settings
        </h3>
        <template v-for="setting in settings" :key="setting">
            <FormInput
                label="Gateway Name"
                v-if="setting === 'name'"
                :error="form.errors.name"
                v-model="form.name"
            />
            <FormInput
                label="Gateway Logo"
                v-else-if="setting === 'logo'"
                :error="form.errors.logo"
                v-model="form.logo"
            />
            <Switch
                v-else-if="setting === 'enable_withdraw'"
                v-model="form.enable_withdraw"
                >Enable Gateway for Withdraw</Switch
            >
            <Switch
                v-else-if="setting === 'enable_deposit'"
                v-model="form.enable_deposit"
                >Enable Gateway for deposits</Switch
            >
            <div v-else-if="setting === 'currencies'">
                <FormInput
                    label="Gateway Currencies"
                    :error="form.errors.currencies"
                    v-model="form.currencies"
                    help="currency symbols separated by comma"
                />
                <p>
                    Ensure the gateway and currency rates api supports
                    currencies you add here
                </p>
            </div>
            <FormInput
                v-else
                :label="titleCase(setting)"
                :error="form.errors[setting]"
                v-model="form[setting]"
            />
        </template>

        <div class="flex items-end justify-end">
            <PrimaryButton
                :disabled="form.processing"
                @click="save"
                class="mt-4"
                primary
            >
                <Loading class="mr-2 -ml-1" v-if="form.processing" />
                Save {{ titleCase(gateway) }} Settings</PrimaryButton
            >
        </div>
    </div>
</template>
