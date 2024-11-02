<script setup>
	import MoneyFormat from "@/Components/MoneyFormat.vue";

	defineProps({
		game: Object,
	});
</script>
<template>
	<div
		class="border-t border-gray-250 dark:border-gray-750 flex justify-start min-w-0 pt-2">
		<div
			class="flex-auto max-w-full flex flex-col flex-nowrap justify-between">
			<div class="flex items-center justify-between">
				<div class="flex items-center flex-nowrap min-w-0">
					<div class="min-w-[100px]">
						<h1
							class="text-[1.7em] text-gray-800 dark:text-white font-extrabold font-inter text-ellipsis overflow-hidden whitespace-nowrap lining-nums">
							<span class="lining-nums">
								{{ game.homeTeam.name }}
							</span>
						</h1>
						<h1
							class="text-[1.7em] text-gray-800 dark:text-white font-extrabold font-inter text-ellipsis overflow-hidden whitespace-nowrap lining-nums">
							<span class="lining-nums">
								{{ game.awayTeam.name }}
							</span>
						</h1>
					</div>
					<div
						v-if="
							game.hasStarted &&
							(game.state == 'in_play' ||
								game.state == 'finished')
						"
						class="inline-block font-bold text-center text-base">
						<div class="inline-block relative align-middle ml-6">
							<div
								:class="
									game.homeScore > game.awayScore
										? ' bg-green-600 '
										: ' bg-gray-700 '
								"
								class="text-white rounded-sm h-[30px] leading-[30px] px-2 relative text-center font-bold font-inter whitespace-nowrap tabular-nums">
								{{ game.homeScore }}
							</div>
							<div class="font-semibold h-2"></div>
							<div
								:class="
									game.awayScore > game.homeScore
										? ' bg-green-600 '
										: ' bg-gray-700 '
								"
								class="text-white rounded-sm h-[30px] leading-[30px] px-2relative text-center font-bold font-inter whitespace-nowrap tabular-nums">
								{{ game.awayScore }}
							</div>
						</div>
					</div>
				</div>
				<slot name="multiples"></slot>
			</div>
			<div class="">
				<div
					class="flex flex-row gap-x-3 gap-y-1 mt-4 items-baseline max-w-full mr-2.5">
					<div
						class="text-[10px] font-bold tracking-[0.7px] uppercase inline-block leading-[1em] text-sky-500">
						<span
							v-if="game.state == 'in_play' && game.elapsed"
							class="lining-nums">
							{{ game.elapsed }} Mins
						</span>
						<span v-else class="lining-nums">{{ game.state }}</span>
					</div>
					<div class="text-[10px] font-bold uppercase">
						<time class="" :datetime="game.startTime">
							{{ game.startTimeGmt }}
						</time>
					</div>
					<div
						class="text-[10px] font-bold tracking-[0.5px] uppercase">
						{{ game.marketsCount }} markets
					</div>
					<div
						class="text-[10px] font-bold tracking-[0.5px] uppercase">
						<MoneyFormat
							billion
							:amount="game.volume"
							#default="{ amount }">
							VOL {{ amount }}
						</MoneyFormat>
					</div>
					<div
						class="text-[10px] font-bold tracking-[0.5px] uppercase">
						<MoneyFormat
							billion
							:amount="game.liquidity"
							#default="{ amount }">
							LQ {{ amount }}
						</MoneyFormat>
					</div>
					<div
						class="text-[10px] font-bold tracking-[0.5px] uppercase">
						Traded
						<MoneyFormat
							billion
							class="text-emeral-600 dark:text-emerald-400"
							:amount="game.traded" />
					</div>
				</div>
				<div
					class="flex justify-end items-center flex-1 whitespace-nowrap"></div>
			</div>
		</div>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
