<script setup>
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});
</script>
<template>
	<div class="space-y-2">
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold mr-2">Amount:</span>
			<MoneyFormat :amount="transaction.transactable.amount" />
		</p>
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold">Total Odds:</span>
			{{ transaction.transactable.total_odds }}
		</p>
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold">Status:</span>
			{{ transaction.transactable.status }}
		</p>
		<div class="mt-2">
			<h4 class="font-semibold text-gray-700 dark:text-gray-200">
				Wagers:
			</h4>
			<ul class="list-disc list-inside">
				<li
					v-for="(wager, index) in transaction.transactable.wagers"
					:key="index"
					class="text-gray-600 dark:text-gray-300">
					{{ wager.bet.name }} - Odds: {{ wager.odds }}
				</li>
			</ul>
		</div>
	</div>
</template>
