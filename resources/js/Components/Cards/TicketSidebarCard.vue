<script setup>
	import { ref } from "vue";

	import { ChevronDown, ChevronsRight } from "lucide-vue-next";

	import WagerCard from "@/Components/Cards/WagerCard.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	defineProps({
		ticket: Object,
	});
	const showWagers = ref(false);
</script>

<template>
	<div class="flex-1 border-b border-gray-200 dark:border-gray-650">
		<a
			href="#"
			@click="showWagers = !showWagers"
			class="leading-[18px] p-2.5 h-4.5 hover:bg-gray-200 dark:hover:bg-gray-700 whitespace-nowrap flex items-center justify-start">
			<div
				class="text-gray-500 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
				<span
					class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
					<MoneyFormat :amount="ticket.amount" />
				</span>
			</div>
			<div
				class="text-gray-500 ml-1 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
				<ChevronsRight
					class="inline-flex text-sky-500 mr-1 self-center w-4 h-4" />
				<span
					class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
					<MoneyFormat :amount="ticket.payout" />
				</span>
			</div>
			<div
				class="text-gray-500 ml-3 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
				{{ $t("Odd") }}
				<span
					class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
					{{ ticket.total_odds }}
				</span>
			</div>
			<StatusBadge class="ml-auto" :status="ticket.status" />
			<ChevronDown
				:class="{ 'rotate-180': showWagers }"
				class="w-4 h-4 ml-2 my-1 transition-transform duration-300" />
		</a>
		<CollapseTransition>
			<div class="" v-show="showWagers">
				<WagerCard
					v-for="wager in ticket.wagers"
					:key="wager.id"
					:wager="wager" />
			</div>
		</CollapseTransition>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
