<script setup>
	import { UseTimeAgo } from "@vueuse/components";
	import { Calendar, ClockIcon } from "lucide-vue-next";
	import { DateTime } from "luxon";

	import OddsFormat from "@/Components/OddsFormat.vue";
	defineProps({
		wager: Object,
	});
</script>

<template>
	<div
		class="bg-gray-50 dark:bg-gray-750 px-2 py-1 last:border-none border-b border-gray-250 dark:border-gray-650">
		<div class="flex items-center relative group">
			<div class="flex flex-1 flex-col">
				<span
					class="flex-1 font-bold text-[13px] min-w-full max-w-0 overflow-hidden text-ellipsis mb-[3px]">
					<a
						class="bg-transparent text-gray-800 dark:text-white font-bold font-inter no-underline"
						:href="
							route('sports.show', {
								sport: wager.sport,
								game: wager.game.slug,
							})
						">
						{{ wager.game.name }}
					</a>
				</span>
				<div class="flex min-w-full space-x-4 items-center">
					<span
						class="text-[10px] font-bold uppercase tracking-[0.8px] overflow-hidden text-ellipsis">
						<a
							class="bg-transparent text-gray-400 dark:text-gray-400 no-underline"
							:href="
								route('sports.show', {
									sport: wager.sport,
									game: wager.game.slug,
								})
							">
							{{ wager.market_info }}
						</a>
					</span>
					<span
						class="overflow-hidden flex items-center space-x-1 text-ellipsis text-[10px] font-bold uppercase tracking-[0.8px]">
						<span class="text-emerald-600 dark:text-emerald-400">
							{{ $t("For") }}
						</span>
						<div
							class="text-gray-800 truncate max-w-[140px] dark:text-white">
							{{ wager.bet_info }}
						</div>
					</span>
				</div>
			</div>
		</div>
		<div
			class="leading-[18px] h-4.5 whitespace-nowrap flex items-center justify-start mt-1">
			<div
				v-if="wager.game"
				:class="[
					{
						'text-sky-500':
							(wager.game.hasStarted &&
								!(
									wager.game.hasEnded || wager.game.stateEnded
								)) ||
							wager.game.state == 'in_play',
					},
					{
						'text-gray-700 dark:text-gray-300':
							!wager.game.hasStarted,
					},
					{
						'text-red-500':
							wager.game.hasEnded || wager.game.stateEnded,
					},
				]"
				class="text-[10px] font-extrabold uppercase inline-block leading-[1em] tracking-[0.7px]">
				<div
					class="flex items-center"
					v-if="
						wager.game.hasStarted &&
						!(wager.game.hasEnded || wager.game.stateEnded)
					">
					<div
						v-if="!wager.game.elapsed"
						class="w-2 h-2 mr-1 relative rounded-full bg-sky-500 dark:border-navy-700">
						<span
							class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-500 opacity-80"></span>
					</div>
					<ClockIcon v-else class="w-4 h-4 mr-0.5 align-middle" />
					<span v-if="wager.game.statusText != 'TBD'" class="mr-2">
						{{ wager.game.statusText }}
					</span>
					<span v-else-if="wager.game.elapsed">
						{{ wager.game.elapsed }}'
					</span>
					<span v-else>{{ $t("Live now") }}</span>
				</div>
				<span
					class="text-red-500"
					v-else-if="wager.game.hasEnded || wager.game.stateEnded">
					{{ $t("Event Ended") }}
				</span>
				<div v-else class="flex items-center">
					<ClockIcon
						v-if="wager.game.isToday"
						class="w-3 h-3 mr-1 align-middle" />
					<Calendar
						:icon="RiCalendarEventFill"
						v-else
						class="w-3 h-3 mr-1 align-middle" />
					<UseTimeAgo
						:time="
							DateTime.fromSeconds(
								wager.game.startTimeTs,
							).toJSDate()
						"
						v-slot="{ timeAgo }">
						<div>{{ timeAgo }}</div>
					</UseTimeAgo>
				</div>
			</div>
			<div class="text-[10px] ml-3 font-bold font-inter">
				X
				<OddsFormat v-if="wager.odds" :odds="wager.odds" />
			</div>
		</div>
	</div>
</template>
