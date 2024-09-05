<script setup>
	import { AlertCircle } from "lucide-vue-next";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import { Badge } from "@/Components/ui/badge";

	defineProps({
		deposit: {
			type: Object,
			required: true,
		},
	});

	const formatDate = (dateString) => {
		return new Date(dateString).toLocaleString();
	};
</script>
<template>
	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Deposit Tracking ID
				</p>
				<p
					class="text-lg uppercase font-semibold text-gray-900 dark:text-white">
					{{ deposit.uid }}
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Current Status
				</p>
				<Badge
					variant="outline"
					class="uppercase font-semibold font-inter"
					:class="{
						'border-gray-400 dark:border-gray-650 text-gray-600 dark:text-gray-400':
							['review', 'pending', 'processing'].includes(
								deposit.status,
							),
						'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
							['approved', 'complete'].includes(deposit.status),
						'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
							['rejected', 'failed', 'reversed'].includes(
								deposit.status,
							),
					}">
					{{ deposit.status }}
				</Badge>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Amount
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					<MoneyFormat
						:amount="deposit.amount"
						:symbol="deposit.amount_currency" />
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Gateway Amount
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					{{
						deposit.gateway_amount
							? deposit.gateway_amount * 1
							: "unknown"
					}}
					{{ deposit.gateway_currency }}
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Gateway
				</p>
				<div class="flex items-center space-x-2">
					<img
						:src="deposit.gateway.logo"
						class="w-7 h-7 rounded-full" />
					<p
						class="text-lg font-semibold text-gray-900 dark:text-white">
						{{ deposit.gateway.name }}
					</p>
				</div>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Created At
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					{{ formatDate(deposit.created_at) }}
				</p>
			</div>
		</div>

		<div
			class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-750"
			v-if="
				(deposit.gateway_error && deposit.status == 'failed') ||
				deposit.status == 'rejected'
			">
			<h3
				class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">
				An error occured at the payment gateway
			</h3>

			<div class="bg-gray-100 dark:bg-gray-750 grid gap-4 p-4 rounded-md">
				<p class="text-sm text-red-600 dark:text-red-400">
					<AlertCircle class="inline-flex mr-2" />
					{{ deposit.gateway_error }}
				</p>
			</div>
		</div>

		<div
			class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-750"
			v-if="deposit.transaction">
			<h3
				class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">
				Associated Transaction
			</h3>
			<div class="mt-6 space-y-2" v-if="deposit.deposit_address">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Deposit Address
				</p>
				<p
					class="text-lg font-semibold text-gray-900 dark:text-white break-all">
					{{ deposit.deposit_address }}
				</p>
			</div>
			<div class="bg-gray-100 dark:bg-gray-700 grid gap-4 p-4 rounded-md">
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium">Transaction ID:</span>
					{{ deposit.transaction.uuid }}
				</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium mr-2">Amount:</span>
					<MoneyFormat :amount="deposit.transaction.amount" />
				</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium mr-3">Type:</span>
					<span
						:class="
							deposit.transaction.action === 'credit'
								? 'text-green-600 dark:text-green-400'
								: 'text-red-600 dark:text-green-400'
						"
						class="uppercase font-semibold">
						{{ deposit.transaction.action }}
					</span>
				</p>
			</div>
		</div>
	</div>
</template>
