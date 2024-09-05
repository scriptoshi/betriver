<script setup>
	import { Link, useForm, usePage } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormInput from "@/Components/FormInput.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";

	defineProps({
		mustVerifyEmail: {
			type: Boolean,
		},
		status: {
			type: String,
		},
	});

	const user = usePage().props.auth.user;

	const form = useForm({
		name: user.name,
		email: user.email,
	});
</script>

<template>
	<section>
		<header>
			<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Name and Email
			</h2>

			<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
				Update your account's name and email address.
			</p>
		</header>

		<form
			@submit.prevent="
				form.patch(route('profile.update'), {
					preserveState: true,
					preserveScroll: true,
				})
			"
			class="mt-6 space-y-6">
			<FormInput
				:label="$t('Name')"
				v-model="form.name"
				required
				autofocus
				autocomplete="name"
				:error="form.errors.name" />
			<FormInput
				:label="$t('Email')"
				v-model="form.email"
				required
				autofocus
				autocomplete="username"
				:error="form.errors.email" />

			<div v-if="mustVerifyEmail && user.email_verified_at === null">
				<p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
					Your email address is unverified.
					<Link
						:href="route('verification.send')"
						method="post"
						as="button"
						class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
						Click here to re-send the verification email.
					</Link>
				</p>

				<div
					v-show="status === 'verification-link-sent'"
					class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
					A new verification link has been sent to your email address.
				</div>
			</div>

			<div class="gap-4">
				<CollapseTransition>
					<p
						v-show="form.recentlySuccessful"
						class="mb-3 text-green-500 dark:text-green-400">
						Saved successfully
					</p>
				</CollapseTransition>
				<PrimaryButton
					class="uppercase text-xs font-semibold"
					:disabled="form.processing">
					Update Profile
				</PrimaryButton>
			</div>
		</form>
	</section>
</template>
