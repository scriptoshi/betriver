<script setup>
	import { ref } from "vue";

	import {
		BarChart2,
		Clock,
		Coins,
		DollarSign,
		FileText,
		HelpCircle,
		RefreshCcw,
		ShieldCheck,
		Target,
		XCircle,
	} from "lucide-vue-next";

	const withdrawalInfo = ref([
		{
			icon: Clock,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Processing Time",
			text: "Withdrawals are typically processed within 24-48 hours. Large amounts may require additional verification and take longer.",
		},
		{
			icon: DollarSign,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Minimum Withdrawal",
			text: "The minimum withdrawal amount is [insert amount and currency/crypto].",
		},
		{
			icon: Coins,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Fees",
			text: "A small fee will be applied to cover transaction costs. This fee will be displayed before you confirm your withdrawal.",
		},
		{
			icon: ShieldCheck,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Verification",
			text: "For security reasons, we may require additional verification for large or unusual withdrawal requests.",
		},
		{
			icon: Target,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Destination Address",
			text: "Double-check your withdrawal address. We cannot recover funds sent to incorrect addresses.",
		},
		{
			icon: RefreshCcw,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Currency Conversion",
			text: "If you're withdrawing to a different currency, conversion rates will apply and may fluctuate.",
		},
		{
			icon: XCircle,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Cancellation",
			text: "Withdrawal requests can usually be cancelled if they haven't been processed yet. Contact support for assistance.",
		},
		{
			icon: BarChart2,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Account Limits",
			text: "Ensure your withdrawal doesn't exceed your account limits. View your limits in your account settings.",
		},
		{
			icon: FileText,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Tax Implications",
			text: "Please be aware that withdrawals may have tax implications. Consult with a tax professional if needed.",
		},
		{
			icon: HelpCircle,
			iconColor: "dark:text-gray-550 text-gray-350",
			title: "Support",
			text: "If you encounter any issues or have questions, please contact our support team at [insert support email/link].",
		},
	]);
</script>
<template>
	<div
		class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md mt-10">
		<h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">
			Important Withdrawal Information
		</h2>

		<ul class="space-y-4">
			<li
				v-for="(item, index) in withdrawalInfo"
				:key="index"
				class="flex items-start">
				<component
					:is="item.icon"
					class="w-6 h-6 mr-2 flex-shrink-0"
					:class="item.iconColor" />
				<div>
					<h3 class="font-semibold text-gray-900 dark:text-white">
						{{ item.title }}
					</h3>
					<p class="text-gray-600 dark:text-gray-300">
						{{ item.text }}
					</p>
				</div>
			</li>
		</ul>
	</div>
</template>
