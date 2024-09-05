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
			<span class="font-semibold">Status:</span>
			{{ transaction.transactable.status }}
		</p>
		<div class="mt-2">
			<h4 class="font-semibold text-gray-700 dark:text-gray-200">
				Commission:
			</h4>
			<p class="text-gray-600 dark:text-gray-300">
				Type: {{ transaction.transactable.commission.type }}, Level:
				{{ transaction.transactable.commission.level }}
			</p>
		</div>
	</div>
</template>
