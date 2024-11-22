<script setup>
	import { Link } from "@inertiajs/vue3";
	import { UseTimeAgo } from "@vueuse/components";
	import { DateTime } from "luxon";
	import { MdAccesstime, RiCalendar2Line } from "oh-vue-icons/icons";

	import MarketButtons from "@/Components/Cards/MarketButtons.vue";
	import MarketSettled from "@/Components/Cards/MarketSettled.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import TeamName from "@/Components/TeamName.vue";
	import ToggleWatchList from "@/Components/ToggleWatchList.vue";
	import VueIcon from "@/Components/VueIcon.vue";

	defineProps({
		game: Object,
		defaultMarketsCount: Number,
		market: Object,
		showBookie: Boolean,
		showExchange: Boolean,
	});
</script>
<template>
	<li
		class="text-gray-700 dark:text-white flex flex-row min-h-[70px] [contain-intrinsic-size:70px] relative transition-[background-color] duration-[0.5s] [contain:content] [content-visibility:auto] will-change-transform content-center m-0 p-0 rounded-sm bg-white dark:bg-gray-800">
		<div
			v-if="game.scores.length"
			class="flex flex-[0_0_auto] justify-center items-center order-1 w-[50px] border-r border-gray-150 dark:border-gray-750 m-0">
			<div class="flex font-bold text-center m-0">
				<div
					class="flex relative align-middle [flex-flow:column] ml-1 m-0">
					<div
						:class="{
							'text-green-500  dark:text-green-400':
								game.homeScore > game.awayScore,
							'text-red-500 dark:text-red-400':
								game.homeScore < game.awayScore,
							'text-gray-700 dark:text-white':
								game.homeScore == game.awayScore,
						}"
						class="relative text-center text-[80%] leading-[1.3rem] font-extrabold font-inter whitespace-nowrap tabular-nums m-0 px-[5px] py-0 rounded-sm">
						{{ game.homeScore }}
					</div>
					<div class="font-semibold h-2 flex-[1_1_6px] m-0"></div>
					<div
						:class="{
							'text-green-500  dark:text-green-400':
								game.homeScore < game.awayScore,
							'text-red-500 dark:text-red-400':
								game.homeScore > game.awayScore,
							'text-gray-700 dark:text-white':
								game.homeScore == game.awayScore,
						}"
						class="relative text-center text-[80%] leading-[1.3rem] font-extrabold font-inter whitespace-nowrap tabular-nums m-0 px-[5px] py-0 rounded-sm">
						{{ game.awayScore }}
					</div>
				</div>
			</div>
		</div>
		<div
			class="flex [flex-flow:column] flex-[1_0_5rem] min-h-[auto] max-w-full min-w-0 order-2 m-0 pl-2.5 pr-3 py-2.5 sm:flex-[1_0_5rem] sm:justify-center sm:pr-0">
			<Link
				class="bg-transparent text-[15px] font-bold w-0 min-w-full text-emerald-600 dark:text-emerald-400 flex flex-wrap lining-nums no-underline sm:pr-2 z-10"
				:href="route('sports.show', { game: game.slug })">
				<div class="flex-[1_1_0px] w-full min-w-0 m-0 sm:flex-auto">
					<div
						class="flex-wrap overflow-hidden text-ellipsis whitespace-nowrap">
						<span
							class="flex-[1_0_0px] overflow-hidden text-ellipsis lining-nums m-0">
							{{ game.homeTeam.name }}
						</span>
					</div>
					<div
						class="flex-wrap overflow-hidden text-ellipsis whitespace-nowrap">
						<span
							class="flex-[1_0_0px] overflow-hidden text-ellipsis lining-nums m-0">
							{{ game.awayTeam.name }}
						</span>
					</div>
				</div>
			</Link>
			<div
				class="leading-[18px] h-4.5 whitespace-nowrap flex items-center justify-start mt-0.5">
				<div
					:class="[
						{
							'text-sky-500':
								(game.hasStarted &&
									!(game.hasEnded || game.stateEnded)) ||
								game.state == 'in_play',
						},
						{
							'text-gray-700 dark:text-gray-300':
								!game.hasStarted,
						},
						{ 'text-red-500': game.hasEnded || game.stateEnded },
					]"
					class="text-[10px] font-extrabold uppercase inline-block leading-[1em] tracking-[0.7px]">
					<div
						class="flex items-center"
						v-if="
							game.hasStarted &&
							!(game.hasEnded || game.stateEnded)
						">
						<div
							v-if="!game.elapsed"
							class="w-2 h-2 mr-2 relative rounded-full bg-sky-500 dark:border-navy-700">
							<span
								class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-500 opacity-80"></span>
						</div>
						<VueIcon
							v-else
							:icon="MdAccesstime"
							class="w-4 h-4 mr-0.5 align-middle" />
						<span v-if="game.statusText != 'TBD'" class="mr-2">
							{{ game.statusText }}
						</span>
						<span v-if="game.elapsed">{{ game.elapsed }}'</span>
						<span v-else>{{ $t("Live now") }}</span>
					</div>
					<span
						class="text-red-500"
						v-else-if="game.hasEnded || game.stateEnded">
						{{ game.statusText }}
					</span>
					<div v-else class="flex items-center">
						<VueIcon
							:icon="MdAccesstime"
							v-if="game.isToday"
							class="w-3 h-3 mr-1 align-middle" />
						<VueIcon
							:icon="RiCalendar2Line"
							v-else
							class="w-3 h-3 mr-1 align-middle" />
						<UseTimeAgo
							v-if="game.startTimeTs"
							:time="
								DateTime.fromSeconds(
									game.startTimeTs,
								).toJSDate()
							"
							v-slot="time">
							<div>{{ time?.timeAgo }}</div>
						</UseTimeAgo>
					</div>
				</div>
				<div
					class="text-gray-400 ml-5 inline-block leading-[1em] text-[10px] font-inter font-semibold tracking-[0.7px] uppercase">
					{{ $t("Traded:") }}
					<span
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
						<MoneyFormat :amount="game.traded ?? 0" />
					</span>
				</div>
				<div
					class="text-gray-400 ml-5 inline-block leading-[1em] text-[10px] font-inter font-semibold tracking-[0.5px] uppercase">
					{{
						`${
							game.marketsCount == 0
								? defaultMarketsCount
								: game.marketsCount
						} Markets active`
					}}
				</div>
			</div>
		</div>
		<template v-if="bettingEnded">
			<MarketSettled />
		</template>
		<div
			v-else
			class="flex flex-[1_0_20rem] pl-4 border-l border-gray-150 dark:border-gray-750 flex-wrap items-center w-2/5 min-w-[20rem] order-4 max-w-[40%] whitespace-nowrap">
			<span
				v-for="bet in market.bets"
				:key="bet.id"
				:class="{
					'order-first': bet.result === 'home',
					'order-last': bet.result === 'away',
				}"
				class="block justify-between items-center rounded text-inherit flex-wrap text-xs leading-[1.125rem] w-[33.3%] lining-nums">
				<span
					class="flex-auto block items-center overflow-hidden text-ellipsis whitespace-nowrap text-center h-[1.125rem] lining-nums mr-1 m-0">
					<TeamName :name="bet.name" :game="game" />
				</span>
				<MarketButtons
					:game="game"
					:bet="bet"
					:market="market"
					:showBookie="showBookie"
					:showExchange="showExchange"
					:odds="game.odds.find((n) => n.bet_id == bet.id)"
					:lay="game.lays.find((n) => n.bet_id == bet.id)"
					:back="game.backs.find((n) => n.bet_id == bet.id)" />
			</span>
		</div>
		<ToggleWatchList
			:gameId="game.id"
			:isWatched="$page.props.auth.watchlist.includes(game.id)" />
		<div class="order-7 w-full"></div>
	</li>
</template>
<style scoped>
	* {
		@apply flex-wrap;
	}
</style>
