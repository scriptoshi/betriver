<script setup>
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import { useBookieForm, useExchangeForm } from "@/Pages/Games/bettingForm";

	const props = defineProps({
		lay: Object,
		back: Object,
		odds: Object,
		bet: Object,
		market: Object,
		game: Object,
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
	const { addBet: addExchnage } = useExchangeForm();
	const { addBet: addBookie } = useBookieForm();
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
</script>
<template>
	<div v-if="showBookie" class="flex flex-wrap mt-0.5 m-0 px-2 py-0">
		<span class="w-full cursor-pointer lining-nums m-0">
			<span
				@click="addBet(odds.odd * 1, true)"
				class="font-extrabold whitespace-nowrap lining-nums text-xs font-inter uppercase block relative text-center transition-[background-color] duration-[0.3s] bg-emerald-600 dark:bg-emerald-600 hover:bg-emerald-700 dark:hover:bg-emerald-500 text-white mr-0.5 m-0 px-0 py-1 rounded-sm">
				<OddsFormat :odds="odds.odd * 1" #default="{ odds }">
					{{ odds }}
				</OddsFormat>
			</span>
		</span>
	</div>
	<div v-else class="flex flex-wrap mt-0.5 m-0 px-2 py-0">
		<span v-if="back" class="w-6/12 cursor-pointer lining-nums m-0">
			<span
				@click="addBet(back.price * 1, true)"
				class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] bg-emerald-600 dark:bg-emerald-500 text-white mr-0.5 m-0 px-0 py-1 rounded-sm">
				<OddsFormat :odds="back.price * 1" #default="{ odds }">
					{{ odds }}
				</OddsFormat>
			</span>
			<span
				class="font-bold whitespace-nowrap lining-nums block relative text-center text-[0.6875rem] leading-[0.8rem] mr-0.5 mt-[5px] m-0 rounded-sm">
				<MoneyFormat
					billion
					:amount="back.amount * 1"
					#default="{ amount }">
					{{ amount }}
				</MoneyFormat>
			</span>
		</span>
		<span v-else class="w-6/12 cursor-pointer lining-nums m-0">
			<span
				@click="addBet('BID', true)"
				class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] bg-emerald-600 dark:bg-emerald-500 text-white mr-0.5 m-0 px-0 py-1 rounded-sm">
				{{ $t("BID") }}
			</span>
		</span>
		<span v-if="lay" class="w-6/12 cursor-pointer lining-nums m-0">
			<span
				@click="addBet(lay.price * 1, false)"
				class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] bg-sky-600 dark:bg-sky-500 text-white ml-0.5 m-0 px-0 py-1 rounded-sm">
				<OddsFormat :odds="lay.price * 1" #default="{ odds }">
					{{ odds }}
				</OddsFormat>
			</span>
			<span
				class="font-bold whitespace-nowrap lining-nums block relative text-center text-[0.6875rem] leading-[0.8rem] ml-0.5 mt-[5px] m-0 rounded-sm">
				<MoneyFormat
					billion
					:amount="lay.amount * 1"
					#default="{ amount }">
					{{ amount }}
				</MoneyFormat>
			</span>
		</span>
		<span v-else class="w-6/12 cursor-pointer lining-nums m-0">
			<span
				@click="addBet('ASK', false)"
				class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] bg-sky-600 dark:bg-sky-500 text-white ml-0.5 m-0 px-0 py-1 rounded-sm">
				{{ $t("ASK") }}
			</span>
		</span>
	</div>
</template>
