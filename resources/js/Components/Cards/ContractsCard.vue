<script setup>
	import { computed, ref } from "vue";

	import { HiChevronDown } from "oh-vue-icons/icons";

	import ContractInfo from "@/Components/Cards/ContractInfo.vue";
	import ContractOpen from "@/Components/Cards/ContractOpen.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import TeamName from "@/Components/TeamName.vue";
	import TinyButton from "@/Components/TinyButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { Market } from "@/hooks/market";

	const props = defineProps({
		game: Object,
		market: Object,
		showBookie: Boolean,
		opened: Boolean,
		showExchange: Boolean,
		handicaps: {
			type: Object,
			default: () => ({}),
		},
	});

	const marketSummary = computed(() => Market.getMarketSummary(props.market));
	const handicap = ref(Object.keys(props.handicaps ?? {})[0] ?? null);

	const isChosen = ref(false);
	const show = ref(props.opened);
	const bets = computed(() => {
		if (handicap.value === null || !handicap.value)
			return props.market.bets;
		const caps = props.handicaps[handicap.value] ?? [];
		if (!caps.length) return props.market.bets;
		return props.market.bets.filter((bet) => caps.includes(bet.result));
	});
	const setHandicap = (hcap) => {
		handicap.value = hcap;
		if (!show.value) show.value = true;
	};
</script>
<template>
	<div
		:class="[
			isChosen
				? 'bg-emerald-50 dark:bg-emerald-800/10 border-gray-300 dark:border-gray-500'
				: 'bg-white dark:bg-gray-850 ',
		]"
		class="content-contain items-center min-h-[4.5rem] rounded-[2px] p-4 relative transition-colors duration-300 box-border flex-wrap m-0">
		<div class="flex justify-between">
			<div>
				<div class="flex items-center space-x-6">
					<div
						class="text-lg flex items-center font-bold font-inter text-gray-800 dark:text-white">
						<TeamName :name="market.name" :game="game" />
					</div>
				</div>
				<div
					v-if="game.closed"
					class="min-h-[19px] pb-1.5 text-gray-600 dark:text-gray-400 font-normal text-xs uppercase">
					<span
						class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1em] lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						{{ $t("Settled") }}
						<span
							class="font-bold whitespace-nowrap lining-nums box-border shrink-0 flex-wrap m-0">
							{{ game.endDate }}
						</span>
					</span>
					<span
						v-if="market.gameMarket?.winning_bet_id"
						class="text-[10px] font-bold tracking-[0.7px] uppercase lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						{{ $t("Winner") }}:
						<span
							class="text-emerald-600 dark:text-emerald-400 lining-nums box-border shrink-0 flex-wrap m-0">
							{{ market.gameMarkets?.[0].winningBet?.name }}
						</span>
					</span>
				</div>
				<div
					class="min-h-[19px] pb-1.5 text-gray-600 dark:text-gray-400 font-normal text-xs uppercase">
					<span
						class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1em] lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						Traded:
						<span
							class="font-bold whitespace-nowrap lining-nums box-border shrink-0 flex-wrap m-0">
							<MoneyFormat :amount="market.traded ?? 0" />
						</span>
					</span>
					<span
						v-if="market.liquidity > 0"
						class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1em] lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						LQ:
						<span
							class="font-bold whitespace-nowrap lining-nums box-border shrink-0 flex-wrap m-0">
							<MoneyFormat :amount="market.liquidity" />
						</span>
					</span>
					<span
						v-if="market.volume > 0"
						class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1em] lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						VOL:
						<span
							class="font-bold whitespace-nowrap lining-nums box-border shrink-0 flex-wrap m-0">
							<MoneyFormat :amount="market.volume" />
						</span>
					</span>
					<span
						v-if="game.state === 'in_play'"
						class="text-[10px] font-bold tracking-[0.7px] uppercase lining-nums box-border shrink-0 flex-wrap mr-4 m-0">
						In-play delay:
						<span
							class="text-emerald-600 dark:text-emerald-400 lining-nums box-border shrink-0 flex-wrap m-0">
							2 MINS
						</span>
					</span>
				</div>
				<div class="max-w-sm sm:max-w-full">
					<div
						v-if="Object.keys(handicaps).length > 0"
						class="flex flex-wrap items-center gap-2 mt-3">
						<TinyButton
							v-for="cap in Object.keys(handicaps)"
							:key="cap"
							:active="cap === handicap && show"
							@click.prevent="setHandicap(cap)"
							class="self-start">
							{{ cap.replace("k", "") }}
						</TinyButton>
						<TinyButton
							:active="handicap === null && show"
							@click="setHandicap(null)"
							class="self-start">
							{{ $t("ALL") }}
						</TinyButton>
					</div>
				</div>
			</div>
			<button
				class="text-xs h-8 w-8 rounded-sm border border-gray-200 dark:border-gray-750 font-semibold text-gray-400 dark:text-gray-500"
				@click="show = !show">
				<VueIcon
					:icon="HiChevronDown"
					class="w-6 h-6 transition-transform duration-300 transform"
					:class="{ 'rotate-180 text-emerald-500': show }" />
			</button>
		</div>
		<CollapseTransition>
			<div class="pt-4" v-show="show">
				<div class="bloc w-full relative">
					<ContractInfo
						titles
						:lay="marketSummary.lay_overround"
						:back="marketSummary.back_overround" />
					<ul v-if="show">
						<ContractOpen
							v-for="bet in bets"
							:key="bet.uid"
							:showBookie="showBookie"
							:showExchange="showExchange"
							v-show="
								(showBookie && bet.has_odds) || showExchange
							"
							:game="game"
							:market="market"
							:bet="bet" />
					</ul>
				</div>
			</div>
		</CollapseTransition>
	</div>
</template>
