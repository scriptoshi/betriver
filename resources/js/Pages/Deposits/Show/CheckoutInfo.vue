<script setup>
	import { ref } from "vue";

	import {
		AlertTriangle,
		CheckCircle,
		Clock,
		HelpCircle,
		Info,
		Lock,
		Shield,
	} from "lucide-vue-next";

	const infoItems = ref([
		{
			icon: CheckCircle,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "Please verify that the deposit address shown matches the one you scanned from the QR code before sending any funds.",
		},
		{
			icon: Info,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "Note that the Bitcoin network will charge a small transaction fee. This fee is not set by us and goes to Bitcoin miners.",
		},
		{
			icon: AlertTriangle,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "The BTC amount shown is calculated based on the current exchange rate. Due to Bitcoin's volatility, this rate may fluctuate.",
		},
		{
			icon: Clock,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: 'You have <span class="font-semibold">30 minutes</span>  to complete this transaction before the payment window expires.',
		},
		{
			icon: Lock,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "For your security, always double-check the deposit address. We will never ask you to send Bitcoin to a different address via email or any other method.",
		},
		{
			icon: Shield,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "After sending the payment, it may take some time for the Bitcoin network to confirm your transaction. Please be patient.",
		},
		{
			icon: HelpCircle,
			iconColor: "dark:text-gray-550 text-gray-350",
			text: "If you encounter any issues during the payment process, please contact our support team .",
		},
	]);
</script>

<template>
	<div
		class="p-6 bg-white dark:bg-gray-800 rounded shadow-md mt-10 text-gray-800 dark:text-gray-200">
		<h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
			Important Information
		</h2>

		<ul class="space-y-4">
			<li
				v-for="(item, index) in infoItems"
				:key="index"
				class="flex items-start">
				<component
					:is="item.icon"
					class="w-6 h-6 mr-2 flex-shrink-0"
					:class="item.iconColor" />
				<span class="" v-html="item.text"></span>
			</li>
		</ul>
	</div>
</template>
