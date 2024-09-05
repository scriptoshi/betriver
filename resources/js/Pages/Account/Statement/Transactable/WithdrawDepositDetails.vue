<template>
	<div class="space-y-2">
		<div class="text-gray-600 dark:text-gray-300 flex justify-between">
			<span class="font-semibold">Payment Gateway:</span>
			<div class="flex items-center space-x-3">
				<span>{{ transaction.transactable.gateway?.name }}</span>
				<img
					class="w-8 h-8"
					:src="transaction.transactable.gateway?.logo" />
			</div>
		</div>
		<p class="text-gray-600 flex justify-between dark:text-gray-300">
			<span class="font-semibold">Gateway Amount:</span>
			<span>
				{{ transaction.transactable.amount }}
				{{ transaction.transactable.gateway_currency }}
			</span>
		</p>
		<p class="text-gray-600 dark:text-gray-300 flex justify-between">
			<span class="font-semibold">Status:</span>
			<span class="uppercase font-bold">
				{{ transaction.transactable.status }}
			</span>
		</p>
	</div>
</template>

<script setup>
	defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});
</script>
