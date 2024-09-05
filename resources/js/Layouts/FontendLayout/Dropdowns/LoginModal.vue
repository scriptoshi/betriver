<script setup>
	import { ref } from "vue";

	import {
		Dialog,
		DialogPanel,
		DialogTitle,
		TransitionChild,
		TransitionRoot,
	} from "@headlessui/vue";
	import { XMarkIcon } from "@heroicons/vue/24/outline";
	import { useForm } from "@inertiajs/vue3";
	import { Eye, EyeOff, Lock, Mail } from "lucide-vue-next";

	import Checkbox from "@/Components/Checkbox.vue";
	import FormInput from "@/Components/FormInput.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";

	defineProps({
		show: Boolean,
	});

	const emit = defineEmits(["close"]);

	const form = useForm({
		email: "",
		password: "",
		remember: false,
	});

	const processing = ref(false);
	const passwordVisible = ref(false);

	const submit = () => {
		processing.value = true;
		form.post(window.route("login"), {
			preserveScroll: true,
			onFinish: () => {
				processing.value = false;
			},
			onSuccess: () => {
				emit("close");
				form.reset();
			},
		});
	};

	const togglePasswordVisibility = () => {
		passwordVisible.value = !passwordVisible.value;
	};
</script>
<template>
	<TransitionRoot appear :show="show" as="template">
		<Dialog as="div" class="relative z-10" @close="$emit('close')">
			<TransitionChild
				as="template"
				enter="duration-300 ease-out"
				enter-from="opacity-0"
				enter-to="opacity-100"
				leave="duration-200 ease-in"
				leave-from="opacity-100"
				leave-to="opacity-0">
				<div class="fixed inset-0 bg-black/25 dark:bg-gray-900/80" />
			</TransitionChild>

			<div class="fixed inset-0 overflow-y-auto">
				<div
					class="flex min-h-full items-center justify-center p-4 text-center">
					<TransitionChild
						as="template"
						enter="duration-300 ease-out"
						enter-from="opacity-0 scale-95"
						enter-to="opacity-100 scale-100"
						leave="duration-200 ease-in"
						leave-from="opacity-100 scale-100"
						leave-to="opacity-0 scale-95">
						<DialogPanel
							class="w-full max-w-md transform overflow-hidden rounded bg-white dark:bg-gray-800 p-10 text-left align-middle shadow-xl transition-all">
							<DialogTitle
								as="h3"
								class="text-3xl font-extrabold font-inter leading-6 text-gray-900 dark:text-gray-100">
								Login
							</DialogTitle>
							<button
								type="button"
								class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
								@click="$emit('close')">
								<span class="sr-only">Close</span>
								<XMarkIcon class="h-6 w-6" aria-hidden="true" />
							</button>
							<form
								@submit.prevent="submit"
								class="grid gap-6 mt-4">
								<FormInput
									id="email"
									type="email"
									size="md"
									v-model="form.email"
									:error="form.errors.email"
									required
									autocomplete="email"
									:label="$t('Email')">
									<template #lead>
										<Mail
											class="w-5 ml-2 h-5 text-gray-500 dark:text-gray-400" />
									</template>
								</FormInput>
								<FormInput
									id="password"
									size="md"
									:type="
										passwordVisible ? 'text' : 'password'
									"
									v-model="form.password"
									:error="form.errors.password"
									required
									autocomplete="current-password"
									:label="$t('Password')">
									<template #lead>
										<Lock
											class="w-5 ml-2 h-5 text-gray-500 dark:text-gray-400" />
									</template>
									<template #trail>
										<button
											type="button"
											class=""
											@click="togglePasswordVisibility">
											<Eye
												class="w-5 h-5 text-green-600 dark:text-green-400"
												v-if="passwordVisible" />
											<EyeOff
												class="w-5 h-5 text-gray-500 dark:text-gray-400"
												v-else />
										</button>
									</template>
								</FormInput>

								<div class="flex items-center justify-between">
									<label
										class="flex text-gray-700 dark:text-gray-200 items-center space-x-2">
										<Checkbox
											id="remember"
											type="checkbox"
											v-model="form.remember" />
										<span>{{ $t("Remember me") }}</span>
									</label>
									<a
										:href="route('password.request')"
										class="text-sm text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
										Forgot your password?
									</a>
								</div>

								<div class="mt-6">
									<PrimaryButton
										class="w-full !py-2"
										primary
										type="submit"
										:disabled="processing">
										{{
											processing
												? "Logging in..."
												: "Login"
										}}
									</PrimaryButton>
								</div>
							</form>
							<div class="p-2">&nbsp;</div>
							<div
								class="pt-4 border-t border-gray-150 dark:border-gray-750 text-center">
								<a
									:href="route('register')"
									class="text-sm text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300">
									{{ $t("Dont have an account ?") }}
								</a>
							</div>
						</DialogPanel>
					</TransitionChild>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>
