<script setup>
	import { computed } from "vue";

	import WeCopy from "@/Components/WeCopy.vue";
	import CheckoutInfo from "@/Pages/Deposits/Show/CheckoutInfo.vue";
	import CountdownTimer from "@/Pages/Deposits/Show/CountdownTimer.vue";
	import QRCode from "@/Pages/Deposits/Show/QRCode.vue";

	const props = defineProps({
		deposit: {
			type: Object,
			required: true,
		},
	});

	const endTime = computed(() => {
		const createdAt = new Date(props.deposit.created_at);
		return createdAt.getTime() + 30 * 60 * 1000; // 15 minutes in milliseconds
	});
</script>
<template>
	<div>
		<div class="bg-white dark:bg-gray-850 rounded shadow-md p-6">
			<div class="mb-6">
				<p
					class="text-base font-semibold text-gray-700 dark:text-gray-300">
					Total Amount to Pay:
				</p>
				<p
					class="text-3xl font-bold font-inter text-green-600 dark:text-green-400">
					{{ deposit.gateway_amount * 1 }}
					{{ deposit.gateway_currency }}
				</p>
			</div>
			<p
				class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
				Scan QR Code:
			</p>
			<div class="flex flex-col md:flex-row gap-4 mb-6">
				<div class="">
					<QRCode :text="deposit.deposit_address" :size="200" />
				</div>
				<div>
					<div class="mb-6">
						<p
							class="text-lg font-semibold text-gray-700 dark:text-gray-300">
							Deposit Address:
						</p>
						<WeCopy :text="deposit.deposit_address" after>
							<p
								class="text-sm text-gray-900 dark:text-white font-semibold font-mono bg-gray-100 dark:bg-gray-700 py-2 px-4 rounded break-all">
								{{ deposit.deposit_address }}
							</p>
						</WeCopy>
					</div>
					<div class="">
						<p
							class="text-lg font-semibold text-gray-700 dark:text-gray-300">
							Time Remaining:
						</p>
						<CountdownTimer :end-time="endTime" />
					</div>
				</div>
			</div>
			<div class="text-sm text-gray-600 dark:text-gray-400">
				<p>
					Please make sure to send the exact amount to the provided
					address.
				</p>
				<p>This deposit request will expire in 15 minutes.</p>
			</div>
		</div>

		<CheckoutInfo />
	</div>
</template>
