<script setup>
	import { AlertCircle, AlertTriangle, Check, Loader } from "lucide-vue-next";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import { Badge } from "@/Components/ui/badge";

	defineProps({
		withdraw: {
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
					Withdraw Tracking ID
				</p>
				<p
					class="text-lg uppercase font-semibold text-gray-900 dark:text-white">
					{{ withdraw.uid }}
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Current Status
				</p>
				<Badge
					variant="outline"
					class="uppercase text-xs font-bold font-inter"
					:class="{
						'border-gray-400 dark:border-gray-650 text-gray-700 dark:text-gray-200':
							['review', 'pending', 'processing'].includes(
								withdraw.status,
							),
						'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
							['approved', 'complete'].includes(withdraw.status),
						'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
							['rejected', 'failed'].includes(withdraw.status),
					}">
					<Loader
						v-if="
							['review', 'pending', 'processing'].includes(
								withdraw.status,
							)
						"
						class="w-3 h-3 mr-1 -ml-0.5" />
					<Check
						v-else-if="
							['approved', 'complete'].includes(withdraw.status)
						"
						class="w-3 h-3 mr-1 -ml-0.5" />
					<AlertTriangle
						v-else-if="
							['rejected', 'failed', 'reversed'].includes(
								withdraw.status,
							)
						"
						class="w-3 h-3 mr-1 -ml-0.5" />
					{{ withdraw.status }}
				</Badge>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Amount
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					<MoneyFormat
						:amount="withdraw.amount"
						:symbol="withdraw.amount_currency" />
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Gateway Amount
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					{{
						withdraw.gateway_amount
							? withdraw.gateway_amount * 1
							: "pending"
					}}
					{{ withdraw.gateway_currency }}
				</p>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Gateway
				</p>
				<div class="flex items-center space-x-2">
					<img
						:src="withdraw.gateway.logo"
						class="w-7 h-7 rounded-full" />
					<p
						class="text-lg font-semibold text-gray-900 dark:text-white">
						{{ withdraw.gateway.name }}
					</p>
				</div>
			</div>

			<div class="space-y-2">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Created At
				</p>
				<p class="text-lg font-semibold text-gray-900 dark:text-white">
					{{ formatDate(withdraw.created_at) }}
				</p>
			</div>
		</div>

		<div
			class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-750"
			v-if="
				(withdraw.gateway_error && withdraw.status == 'failed') ||
				withdraw.status == 'rejected'
			">
			<h3
				class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">
				An error occured at the payment gateway
			</h3>

			<div class="bg-gray-100 dark:bg-gray-750 grid gap-4 p-4 rounded-md">
				<p class="text-sm text-red-600 dark:text-red-400">
					<AlertCircle class="inline-flex mr-2" />
					{{ withdraw.gateway_error }}
				</p>
			</div>
		</div>

		<div
			class="mt-5 pt-5 border-t border-gray-200 dark:border-gray-750"
			v-if="withdraw.transaction">
			<h3
				class="text-xl font-semibold mb-2 text-gray-800 dark:text-white">
				Associated Transaction
			</h3>
			<div class="mt-6 space-y-2" v-if="withdraw.withdraw_address">
				<p class="text-sm font-medium text-gray-500 dark:text-gray-400">
					Withdraw Address
				</p>
				<p
					class="text-lg font-semibold text-gray-900 dark:text-white break-all">
					{{ withdraw.withdraw_address }}
				</p>
			</div>
			<div class="bg-gray-100 dark:bg-gray-700 grid gap-4 p-4 rounded-md">
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium">Transaction ID:</span>
					{{ withdraw.transaction.uuid }}
				</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium mr-2">Amount:</span>
					<MoneyFormat :amount="withdraw.transaction.amount" />
				</p>
				<p class="text-sm text-gray-600 dark:text-gray-300">
					<span class="font-medium mr-3">Type:</span>
					<span
						:class="
							withdraw.transaction.action === 'credit'
								? 'text-green-600 dark:text-green-400'
								: 'text-red-600 dark:text-red-400'
						"
						class="uppercase font-semibold">
						{{ withdraw.transaction.action }}
					</span>
				</p>
			</div>
		</div>
	</div>
</template>
