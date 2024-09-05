<script setup>
	import { computed } from "vue";

	import { AlertTriangle, Clock, Power } from "lucide-vue-next";

	import PrimaryButton from "@/Components/PrimaryButton.vue";

	const props = defineProps({
		personal: {
			type: Object,
			required: true,
		},
	});

	const timeoutEndsAt = computed(() => {
		if (props.personal.time_out_at) {
			const endDate = new Date(props.personal.time_out_at);
			return endDate.toLocaleString();
		}
		return null;
	});

	const timeRemaining = computed(() => {
		if (props.personal.time_out_at) {
			const now = new Date();
			const endDate = new Date(props.personal.time_out_at);
			const diffTime = Math.abs(endDate - now);
			const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
			return `${diffDays} day${diffDays !== 1 ? "s" : ""}`;
		}
		return null;
	});
</script>
<template>
	<div
		class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center px-4">
		<div
			class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
			<div class="flex items-center justify-center mb-6">
				<AlertTriangle class="text-yellow-500 w-12 h-12 mr-4" />
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">
					Account Timeout
				</h1>
			</div>

			<p class="text-gray-700 dark:text-gray-300 mb-6">
				You are currently under a voluntary timeout. During this period,
				you will not be able to place bets or use the website.
			</p>

			<div
				class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-500 p-4 mb-6">
				<p class="text-yellow-700 dark:text-yellow-300">
					A timeout is an opt-in, temporary break from betting . You
					can choose a timeout period of one, seven, 31 or 42 days.
				</p>
			</div>

			<div v-if="timeoutEndsAt" class="flex items-center mb-4">
				<Clock class="text-sky-500 w-6 h-6 mr-2" />
				<p class="text-gray-700 dark:text-gray-300">
					Your timeout will end on:
					<span class="font-semibold">{{ timeoutEndsAt }}</span>
				</p>
			</div>

			<p
				v-if="timeRemaining"
				class="text-gray-700 dark:text-gray-300 mb-6">
				Time remaining:
				<span class="font-semibold">{{ timeRemaining }}</span>
			</p>

			<p class="text-gray-700 dark:text-gray-300 mb-6">
				When the timeout expires, your account will be automatically
				reopened.
			</p>

			<div
				class="bg-sky-100 dark:bg-sky-900 border-l-4 border-sky-500 p-4">
				<p class="text-sky-700 dark:text-sky-300">
					If you need support or have any questions during your
					timeout period, please contact our customer support team.
				</p>
			</div>
			<div class="flex justify-end">
				<PrimaryButton
					method="post"
					:href="route('logout')"
					link
					secondary
					class="mt-4">
					<Power class="w-4 h-4 mr-2 -ml-1" />
					Logout
				</PrimaryButton>
			</div>
		</div>
	</div>
</template>
