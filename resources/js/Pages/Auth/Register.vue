<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";

import FormInput from "@/Components/FormInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(window.route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Register" />
        <div class="w-full max-w-md">
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>
            <div class="text-start mb-8">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                    Create Your Account 👋
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    By continuing, you agree to our User Agreement and
                    acknowledge that you understand the Privacy Policy.
                </p>
            </div>
            <form @submit.prevent="submit">
                <FormInput
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    label="Name"
                    :error="form.errors.name"
                />
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
                    <Link
                        :href="route('login')"
                        class="font-medium text-amber-600 hover:text-amber-500"
                        >Already registered?</Link
                    >

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        primary
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
