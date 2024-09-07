<script setup>
	import { ChevronRight, ClockIcon, Star } from "lucide-vue-next";

	import TeamName from "../TeamName.vue";
	import OddsInfo from "./OddsInfo.vue";
	defineProps({
		game: Object,
		defaultMarket: Object,
		showBookie: Boolean,
	});
</script>

<template>
	<li
		class="flex flex-col min-h-[131px] [contain-intrinsic-size:115px] relative transition-color duration-500 contain-strict [content-visibility:auto] will-change-transform bg-white dark:bg-gray-800 pt-2 pb-1 px-3.5 rounded-sm">
		<div class="flex flex-col flex-1 min-h-0 max-w-full">
			<div
				class="text-[9px] font-bold font-inter tracking-[0.7px] uppercase max-w-full min-h-[1.125rem] overflow-hidden whitespace-nowrap flex flex-wrap items-center">
				<a
					class="bg-transparent text-[9px] font-bold tracking-[0.7px] uppercase relative transition-[background-color] duration-[0.5s] inline-block text-emerald-600 dark:text-emerald-500 no-underline p-1 rounded-md"
					:href="route('sports.index', { sport: game.sport })">
					<span class="">{{ game.sport }}</span>
				</a>
				<ChevronRight class="w-3 h-3" />
				<a
					class="bg-transparent text-[9px] font-bold tracking-[0.7px] uppercase flex-initial overflow-hidden text-ellipsis relative transition-[background-color] duration-[0.5s] inline-block text-emerald-600 dark:text-emerald-500 no-underline p-1 rounded-md"
					:href="
						route('sports.index', {
							sport: game.sport,
							region: game.league.slug,
						})
					">
					{{ game.league.name }}
				</a>
			</div>
			<a
				class="bg-transparent text-[12px] font-extrabold font-inter w-0 min-w-full text-gray-800 dark:text-white flex flex-wrap max-h-[2.8125rem] leading-[1.2rem] overflow-hidden no-underline"
				:href="
					route('sports.show', {
						game: game.slug,
					})
				">
				<div class="flex-[1_1_0px] w-full min-w-0">
					<div
						class="flex-wrap overflow-hidden text-ellipsis whitespace-nowrap">
						<span class="overflow-hidden text-ellipsis">
							{{ game.name }}
						</span>
					</div>
				</div>

				<button
					class="items-center text-gray-300 dark:text-gray-700 hover:text-emerald-500 dark:hover:text-emerald-400 block transition-colors duration-[0.4s,opacity] delay-200 absolute z-[1] overflow-visible text-center bg-transparent [outline:none] cursor-pointer p-0 rounded-[1px] border-[none] right-4 top-2"
					title="Add to Watchlist">
					<Star class="w-4 h-4" />
				</button>
			</a>
			<div
				class="flex flex-auto flex-wrap items-center w-0 min-w-full whitespace-nowrap">
				<span
					v-for="bet in defaultMarket.bets"
					:key="bet.id"
					class="block justify-between items-center rounded text-inherit flex-wrap text-xs w-[33.3%]">
					<span
						class="flex-auto block items-center overflow-hidden text-ellipsis whitespace-nowrap h-[1.125rem] mr-1">
						<TeamName :game="game" :name="bet.name" />
					</span>
					<OddsInfo
						:showBookie="showBookie"
						:odds="game.odds.find((t) => t.bet_id === bet.id)"
						:trade="game.trades.find((t) => t.bet_id === bet.id)" />
				</span>
			</div>
			<div
				class="flex justify-between items-end text-gray-500 dark:text-gray-400 [contain:strict] h-5 pointer-events-none mt-0.5 pt-0.5 mb-px border-t border-gray-200 dark:border-gray-650 border-solid">
				<div class="text-[10px] flex items-center font-bold uppercase">
					<ClockIcon
						class="w-3 h-3 text-emerald-500 dark:text-emerald-400 mr-1" />
					<time class="" datetime="2024-09-04T09:00:00Z">
						in 1 hour
					</time>
				</div>
				<span
					class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1.2em] grow text-right mt-px">
					Traded:
					<span
						class="font-bold text-emerald-500 dark:text-emerald-400 whitespace-nowrap">
						£2,680
					</span>
				</span>
			</div>
		</div>
	</li>
</template>
