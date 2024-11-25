<script setup>
	import { ref } from "vue";

	import { Link, useForm } from "@inertiajs/vue3";
	import { HiEye, HiEyeOff } from "oh-vue-icons/icons";

	import CheckBox from "@/Components/Checkbox.vue";
	import FormInput from "@/Components/FormInput.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AuthLayout from "@/Layouts/AuthLayout.vue";
	import FacebookLogin from "@/Pages/Auth/FacebookLogin.vue";
	import GithubLogin from "@/Pages/Auth/GithubLogin.vue";
	import GoogleOneTap from "@/Pages/Auth/GoogleOneTap.vue";
	defineProps({
		canResetPassword: {
			type: Boolean,
		},
		status: {
			type: String,
		},
	});

	const form = useForm({
		email: "",
		password: "",
		remember: false,
	});

	const handleSubmit = () => {
		form.post(window.route("login"), {
			onFinish: () => form.reset("password"),
		});
	};

	const showPassword = ref(false);

	const togglePassword = () => {
		showPassword.value = !showPassword.value;
	};
</script>

<template>
	<AuthLayout>
		<div class="w-full max-w-md">
			<div v-if="status" class="mb-4 font-medium text-sm text-green-600">
				{{ status }}
			</div>
			<div class="text-start mb-8">
				<h2 class="text-3xl font-bold text-gray-800 dark:text-white">
					Welcome Back 👋
				</h2>
				<p class="text-gray-600 dark:text-gray-300">
					Enter the information you entered while registering.
				</p>
			</div>
			<form @submit.prevent="handleSubmit" class="space-y-6">
				<FormInput
					label="Email"
					v-model="form.email"
					:error="form.errors.email"
					required
					autofocus
					autocomplete="username" />
				<FormInput
					v-model="form.password"
					:error="form.errors.password"
					label="Password"
					:type="showPassword ? 'text' : 'password'">
					<template #trail>
						<button
							type="button"
							@click="togglePassword"
							class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
							<VueIcon
								class="h-5 w-5 text-emerald-500"
								:icon="HiEye"
								v-if="showPassword" />
							<VueIcon
								v-else
								class="h-5 w-5 text-gray-400"
								:icon="HiEyeOff" />
						</button>
					</template>
				</FormInput>

				<div class="flex items-center justify-between">
					<div class="flex items-center">
						<label
							class="flex text-gray-700 dark:text-gray-200 items-center space-x-2">
							<CheckBox
								id="remember"
								type="checkbox"
								v-model="form.remember" />
							<span>{{ $t("Remember me") }}</span>
						</label>
					</div>
					<div class="text-sm">
						<Link
							v-if="canResetPassword"
							:href="route('password.request')"
							class="font-medium text-amber-600 hover:text-amber-500">
							Forgot your password?
						</Link>
					</div>
				</div>
				<div class="flex space-x-5 items-center">
					<PrimaryButton
						type="submit"
						primary
						:disabled="form.processing">
						Log in
					</PrimaryButton>
					<GoogleOneTap
						v-if="
							$page.props.googleClientId &&
							!!$page.props.googleClientId
						"
						:clientId="$page.props.googleClientId" />
					<FacebookLogin v-if="$page.props.enableFacebookLogin" />
					<GithubLogin v-if="$page.props.enableFacebookLogin" />
				</div>
			</form>

			<p class="mt-10 text-start text-sm text-gray-500">
				Don't have an account?
				<Link
					:href="route('register')"
					class="font-medium text-amber-600 hover:text-amber-500">
					Sign Up
				</Link>
			</p>
		</div>
	</AuthLayout>
</template>
