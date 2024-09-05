<script setup>
	defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});
</script>
<template>
	<div class="space-y-2">
		<p>{{ $t("Your Level was updated recently") }}</p>
		<p class="text-gray-600 dark:text-gray-300 flex justify-between">
			<span class="font-semibold mr-2">Your Current Level:</span>
			<span>{{ transaction.transactable.levelConfig.name }}</span>
		</p>
		<div class="text-gray-600 dark:text-gray-300">
			<div class="font-semibold">About this Level:</div>
			<div class="text-sm">
				{{
					$t(transaction.transactable.levelConfig.description, {
						...transaction.transactable.levelConfig,
					})
				}}
			</div>
			<h3 class="text-gray-800 dark:text-gray-250 text-sm">
				{{
					$t(transaction.transactable.levelConfig.limits, {
						...transaction.transactable.levelConfig,
					})
				}}
			</h3>
		</div>
	</div>
</template>
