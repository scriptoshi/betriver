<script setup>
import { Head, useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(window.route("password.email"));
};
</script>

<template>
    <AuthLayout>
        <Head title="Forgot Password" />
        <div class="w-full max-w-md">
            <div class="text-start mb-8">
                <h2
                    class="text-3xl mb-3 font-bold text-gray-800 dark:text-white"
                >
                    Forgot your password ?
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    No problem. Just let us know your email address and we will
                    email you a password reset link that will allow you to
                    choose a new one.
                </p>
            </div>

            <div
                v-if="status"
                class="mb-4 font-medium text-sm text-green-600 dark:text-green-400"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <FormInput
                    label="Email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    :error="form.errors.email"
                />

                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        primary
                    >
                        Email Password Reset Link
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
