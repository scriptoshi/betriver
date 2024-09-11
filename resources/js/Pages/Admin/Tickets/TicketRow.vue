<script setup>
	import { ref } from "vue";

	import WagerCard from "@/Components/Cards/WagerCard.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	import Switch from "@/Components/Switch.vue";

	defineProps({ ticket: Object });
	const isOpen = ref(false);
</script>
<template>
	<tr
		:class="{ 'bg-gray-100 dark:bg-gray-750': isOpen }"
		class="transition-colors duration-300"
		role="row">
		<td
			class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
			<div>
				<div class="underline">
					{{ ticket.user.email }}
				</div>
				<div>
					<span class="text-emerald-500">
						{{ ticket.wagers.length }}
					</span>
					Bets Acummulated
				</div>
			</div>
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			{{ ticket.uid }}
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<MoneyFormat :amount="ticket.amount" />
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<MoneyFormat :amount="ticket.payout" />
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<OddsFormat :odds="ticket.total_odds" />
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<StatusBadge :status="ticket.status" />
		</td>
		<td
			class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<Switch disabled :modelValue="ticket.won" />
		</td>
		<td
			class="px-6 py-4 text-right whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
			<a
				@click.prevent="isOpen = !isOpen"
				class="text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-500 hover:underline"
				href="#">
				{{ isOpen ? "Less" : "More" }}
			</a>
		</td>
	</tr>

	<tr class="p-0 m-0 border-none">
		<td class="!p-0 m-0 border-none" colspan="8">
			<CollapseTransition>
				<div v-show="isOpen" class="grid p-4 sm:grid-cols-2 gap-1">
					<div
						v-for="wager in ticket.wagers"
						:key="wager.id"
						class="flex items-center">
						<WagerCard class="border-none flex-1" :wager="wager" />
						<div
							class="bg-gray-50 h-full flex items-center dark:bg-gray-750 px-2 py-1">
							<StatusBadge :status="wager.status" />
						</div>
					</div>
				</div>
			</CollapseTransition>
		</td>
	</tr>
</template>
