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
			<span class="font-semibold">Type:</span>
			{{ transaction.transactable.type }}
		</p>
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold mr-2">Amount:</span>
			<MoneyFormat :amount="transaction.transactable.amount" />
		</p>
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold">Odds:</span>
			{{ transaction.transactable.odds }}
		</p>
		<p class="text-gray-600 dark:text-gray-300">
			<span class="font-semibold">Status:</span>
			{{ transaction.transactable.status }}
		</p>
	</div>
</template>
