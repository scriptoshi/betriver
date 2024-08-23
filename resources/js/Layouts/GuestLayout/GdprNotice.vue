<script setup>
	import { onMounted, ref } from "vue";

	import PrimaryButton from "@/Components/PrimaryButton.vue";

	const showNotification = ref(false);
	// localStorage.removeItem("cookieConsent");
	const checkCookieConsent = () => {
		const cookieConsent = localStorage.getItem("cookieConsent");
		showNotification.value = cookieConsent !== "accepted";
	};

	const acceptCookies = () => {
		localStorage.setItem("cookieConsent", "accepted");
		showNotification.value = false;
	};

	onMounted(() => {
		// Slight delay to ensure the transition is visible on initial load
		setTimeout(() => {
			checkCookieConsent();
		}, 100);
	});
</script>

<template>
	<Transition
		enter-active-class="transition ease-out duration-300"
		enter-from-class="transform translate-y-full opacity-0"
		enter-to-class="transform translate-y-0 opacity-100"
		leave-active-class="transition ease-in duration-300"
		leave-from-class="transform translate-y-0 opacity-100"
		leave-to-class="transform translate-y-full opacity-0">
		<div
			v-if="showNotification"
			class="fixed bottom-4 max-w-lg right-1 lg:right-4 p-4 bg-white dark:bg-gray-800 border-t-4 dark:border-gray-600 shadow-lg">
			<h2
				class="text-lg lg:text-xl mb-4 block font-bold leading-tight text-gray-600 dark:text-gray-300">
				{{ $page.props.gdprTitle }}
			</h2>
			<div class="lg:flex lg:space-x-5">
				<p class="mb-5 font-medium text-gray-600">
					{{ $page.props.gdprText }}
				</p>
			</div>
			<div class="flex w-full justify-end">
				<PrimaryButton @click="acceptCookies" secondary>
					I agree
				</PrimaryButton>
			</div>
		</div>
	</Transition>
</template>
