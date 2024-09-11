<script setup>
	import { computed } from "vue";

	import { ChevronsRight } from "lucide-vue-next";

	import BetButton from "@/Components/Cards/BetButton.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import TeamName from "@/Components/TeamName.vue";
	import { Badge } from "@/Components/ui/badge/index";
	import { useBookieForm, useExchangeForm } from "@/Pages/Games/bettingForm";

	const props = defineProps({
		bet: Object,
		game: Object,
		market: Object,
		showBookie: Boolean,
		showExchange: Boolean,
	});

	const getName = (name) => {
		return `${name}`
			.replace("{{", "{")
			.replace("}}", "}")
			.replace("{home}", props.game.homeTeam.name)
			.replace("{away}", props.game.awayTeam.name);
	};
	const { addBet: addExchnage, exchangeForm } = useExchangeForm();
	const { addBet: addBookie, bookieForm } = useBookieForm();
	const addBet = (price, isLay) => {
		const betData = {
			guid: `${props.game.id}-${props.bet.id}`,
			bet: getName(props.bet.name),
			market: getName(props.market.name),
			game: getName(props.game.name),
			bet_id: props.bet.id,
			market_id: props.market.id,
			game_id: props.game.id,
			stake: null,
			returns: null,
			liability: null,
			price: ["BID", "ASK"].includes(price) ? 2.5 : price,
			isLay,
			isAsk: price === "ASK",
			isBid: price === "BID",
		};
		if (props.showBookie) {
			addBookie(betData);
			return;
		}
		addExchnage(betData);
	};
	const exchangeBet = computed(
		() => exchangeForm.value[`${props.game.id}-${props.bet.id}`],
	);
	const bookieBet = computed(
		() => bookieForm.value[`${props.game.id}-${props.bet.id}`],
	);
	const payout = computed(() => {
		return exchangeBet.value.stake * exchangeBet.value.price;
	});
</script>
<template>
	<li>
		<div
			class="border-t border-gray-250 dark:border-gray-750 flex items-center flex-nowrap min-h-[70px]">
			<div class="flex-[1_1_12rem] min-w-0">
				<div class="pb-[0.3rem]">
					<div class="lining-nums">
						<TeamName :name="bet.name" :game="game" />
					</div>
					<div class="flex items-center mt-1">
						<Badge
							variant="outline"
							v-if="showBookie && !!bookieBet"
							class="py-px uppercase font-bold text-[10px] !border-sky-500 !text-sky-500">
							{{ $t("Multiple") }}
							<span
								class="flex items-center text-white ml-2 space-x-1">
								<span>{{ $t("Odds") }}</span>
								<ChevronsRight
									class="inline-flex w-4 h-4 dark:text-emerald-400 text-emerald-600" />

								<span>{{ bet.odds[0]?.odd }}</span>
							</span>
						</Badge>

						<Badge
							v-if="showExchange && !!exchangeBet"
							variant="outline"
							class="py-px uppercase font-bold text-[10px] !border-sky-500 !text-sky-500">
							{{ $t("Bet Slip") }}
							<span
								class="flex items-center text-white ml-2 space-x-1">
								<MoneyFormat
									class="text-[10px]"
									:amount="exchangeBet.stake ?? 0" />
								<ChevronsRight
									class="inline-flex w-4 h-4 dark:text-emerald-400 text-emerald-600" />
								<MoneyFormat
									class="text-[10px]"
									:amount="payout ?? 0" />
							</span>
						</Badge>
					</div>
				</div>
				<div class="flex items-center"></div>
			</div>
			<div
				title="Last trade price"
				class="rounded flex-col cursor-default relative flex-[0_0_4rem] text-center pr-3 text-sm font-bold uppercase lining-nums">
				<div>
					<div class="font-bold whitespace-nowrap lining-nums">
						{{ bet.last_trade?.percentage_price ?? 0 }}%
					</div>
					<div
						class="font-bold whitespace-nowrap lining-nums inline-block text-[10px] h-[11px] leading-[11px] opacity-60 min-w-[1.5rem] px-0.5 py-0 rounded-sm">
						{{ bet.last_trade?.price ?? 0 }}
					</div>
				</div>
			</div>
			<template v-if="showBookie">
				<span
					class="flex flex-[5_0_3.75rem] h-14 mb-[-0.5rem] max-w-[180px] overflow-hidden lining-nums">
					<BetButton
						:price="bet.odds[0]?.odd"
						@go="addBet"
						:amount="null"
						lay
						active
						:ask="null" />
				</span>
				<span
					class="flex flex-[5_0_3.75rem] flex-row h-14 mb-[-0.5rem] max-w-[180px] overflow-hidden lining-nums">
					<BetButton :price="null" :amount="null" blank />
				</span>
			</template>
			<template v-else>
				<span
					class="flex flex-[5_0_3.75rem] h-14 mb-[-0.5rem] max-w-[180px] overflow-hidden lining-nums">
					<BetButton
						@go="addBet"
						:price="bet.lays[2]?.price"
						:amount="bet.lays[2]?.amount"
						:blank="!bet.lays[2]" />
					<BetButton
						@go="addBet"
						:price="bet.lays[1]?.price"
						:amount="bet.lays[1]?.amount"
						:blank="!bet.lays[1]" />
					<BetButton
						@go="addBet"
						:price="bet.lays[0]?.price"
						:amount="bet.lays[0]?.amount"
						active
						:ask="bet.lays[0] ? null : 'BID'" />
				</span>
				<span
					class="flex flex-[5_0_3.75rem] flex-row h-14 mb-[-0.5rem] max-w-[180px] overflow-hidden lining-nums">
					<BetButton
						@go="addBet"
						:price="bet.backs[0]?.price"
						:amount="bet.backs[0]?.amount"
						active
						lay
						:ask="bet.backs[0] ? null : 'ASK'" />
					<BetButton
						@go="addBet"
						lay
						:price="bet.backs[1]?.price"
						:amount="bet.backs[1]?.amount"
						:blank="!bet.backs[1]" />
					<BetButton
						@go="addBet"
						lay
						:price="bet.backs[2]?.price"
						:amount="bet.backs[2]?.amount"
						:blank="!bet.backs[2]" />
				</span>
			</template>
		</div>
	</li>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
