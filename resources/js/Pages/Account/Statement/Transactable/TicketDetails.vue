<script setup>
	import WagerCard from "@/Components/Cards/WagerCard.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	defineProps({
		transaction: {
			type: Object,
			required: true,
		},
	});
</script>
<template>
	<div class="">
		<div class="flex items-center justify-between">
			<p class="text-gray-600 dark:text-gray-300">
				<span class="font-semibold mr-2">Stake:</span>
				<MoneyFormat :amount="transaction.transactable.amount" />
			</p>
			<p class="text-gray-600 dark:text-gray-300">
				<span class="font-semibold mr-2">Total Odds:</span>
				<OddsFormat :odds="transaction.transactable.total_odds" />
			</p>
			<StatusBadge :status="transaction.transactable.status" />
		</div>
		<div class="mt-2">
			<ul class="grid gap-1">
				<li
					v-for="(wager, index) in transaction.transactable.wagers"
					:key="index">
					<WagerCard :wager />
				</li>
			</ul>
		</div>
	</div>
</template>
