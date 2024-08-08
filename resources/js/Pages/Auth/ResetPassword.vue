<script setup>
import { Head, useForm } from "@inertiajs/vue3";

import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(window.route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Reset Password" />
        <div class="w-full max-w-md">
            <form @submit.prevent="submit">
                <FormInput
                    v-model="form.email"
                    required
                    autocomplete="username"
                    label="Email"
                    :error="form.errors.email"
                    class="mt-4"
                />

                <div class="mt-4 gap-4 grid md:grid-cols-2">
                    <FormInput
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        label="Password"
                        :error="form.errors.password"
                        class="mt-4"
                    />
                    <FormInput
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        label="Confirm Password"
                        :error="form.errors.password_confirmation"
                        class="mt-4"
                    />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        primary
                    >
                        Reset Password
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
