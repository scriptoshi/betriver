<script setup>
	import { ref } from "vue";

	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormInput from "@/Components/FormInput.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";

	const passwordInput = ref(null);
	const currentPasswordInput = ref(null);

	const form = useForm({
		current_password: "",
		password: "",
		password_confirmation: "",
	});

	const updatePassword = () => {
		form.put(window.route("password.update"), {
			preserveScroll: true,
			onSuccess: () => form.reset(),
			onError: () => {
				if (form.errors.password) {
					form.reset("password", "password_confirmation");
					passwordInput.value.focus();
				}
				if (form.errors.current_password) {
					form.reset("current_password");
					currentPasswordInput.value.focus();
				}
			},
		});
	};
</script>

<template>
	<section>
		<header>
			<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Modify Password
			</h2>

			<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
				Ensure your account is using a long, random password to stay
				secure.
			</p>
		</header>

		<form @submit.prevent="updatePassword" class="mt-6 space-y-6">
			<FormInput
				:label="$t('Current Password')"
				v-model="form.current_password"
				type="password"
				ref="currentPasswordInput"
				id="current_password"
				autocomplete="current-password"
				:error="form.current_password.email" />

			<div class="grid sm:grid-cols-2 gap-4">
				<FormInput
					:label="$t('New Password')"
					id="password"
					ref="passwordInput"
					v-model="form.password"
					type="password"
					autocomplete="new-password"
					:error="form.errors.password" />
				<FormInput
					id="password_confirmation"
					:label="$t('Confirm password')"
					v-model="form.password_confirmation"
					type="password"
					autocomplete="new-password"
					:error="form.errors.password_confirmation" />
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
					Update Password
				</PrimaryButton>
			</div>
		</form>
	</section>
</template>
