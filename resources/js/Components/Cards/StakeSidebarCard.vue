<script setup>
	import { computed } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { UseTimeAgo } from "@vueuse/components";
	import { Calendar, ChevronsRight, ClockIcon } from "lucide-vue-next";
	import { DateTime } from "luxon";

	import Loading from "@/Components/Loading.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";

	const props = defineProps({
		stake: Object,
		showActions: {
			type: Boolean,
			default: true,
		},
	});
	const game = computed(() => props.stake.game);
	const isBack = computed(() => props.stake.type === "back");
	const tradeOutForm = useForm({
		stake_id: props.stake.id,
	});

	const cancelForm = useForm({ stake: props.stake.id });
	const cancelBet = () => {
		cancelForm.delete(
			window.route("stakes.cancel", { stake: props.stake.id }),
			{
				preserveScroll: true,
				preserveState: true,
			},
		);
	};
</script>

<template>
	<div class="flex-1 p-2.5 border-b border-gray-200 dark:border-gray-650">
		<div class="flex items-center relative group">
			<div class="flex flex-1 flex-col">
				<span
					class="flex-1 font-bold text-[13px] min-w-full max-w-0 overflow-hidden text-ellipsis mb-[3px]">
					<a
						class="bg-transparent text-gray-800 dark:text-white font-bold font-inter no-underline"
						:href="
							route('sports.show', {
								sport: stake.sport,
								game: stake.game.slug,
							})
						">
						{{ stake.game.name }}
					</a>
				</span>
				<div class="flex min-w-full gap-2 items-center">
					<span
						class="text-[10px] font-bold uppercase tracking-[0.8px] overflow-hidden text-ellipsis">
						<a
							class="bg-transparent text-gray-400 dark:text-gray-400 no-underline"
							:href="
								route('sports.show', {
									sport: stake.sport,
									game: stake.game.slug,
								})
							">
							{{ stake.market_info }}
						</a>
					</span>
					<span
						class="overflow-hidden flex items-center space-x-1 text-ellipsis text-[10px] font-bold uppercase tracking-[0.8px]">
						<span
							v-if="isBack"
							class="text-emerald-600 ml-auto dark:text-emerald-400">
							{{ $t("For") }}
						</span>
						<span v-else class="text-sky-600 dark:text-sky-400">
							{{ $t("Against") }}
						</span>

						<div
							class="text-gray-800 truncate max-w-[140px] inline-flex dark:text-white">
							{{ stake.bet_info }}
						</div>
					</span>
				</div>
			</div>
		</div>
		<div>
			<div
				class="leading-[18px] h-4.5 whitespace-nowrap flex items-center justify-start mt-1">
				<div
					class="text-gray-500 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					<span
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
						<MoneyFormat :amount="stake.amount" />
					</span>
				</div>
				<div
					class="text-gray-500 ml-1 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					<ChevronsRight
						class="inline-flex text-sky-500 mr-1 self-center w-4 h-4" />
					<span
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
						<MoneyFormat :amount="stake.payout" />
					</span>
				</div>
				<div
					class="text-gray-500 ml-3 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					{{ $t("Price:") }}
					<OddsFormat
						:odds="stake.price"
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap" />
				</div>
				<div
					class="text-gray-500 ml-3 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					{{ $t("Liable:") }}
					<span
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
						<MoneyFormat :amount="stake.liability" />
					</span>
				</div>
			</div>
			<div
				v-if="game"
				class="leading-[18px] h-4.5 whitespace-nowrap flex items-center justify-start mt-1">
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
							class="w-2 h-2 mr-1 relative rounded-full bg-sky-500 dark:border-navy-700">
							<span
								class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-500 opacity-80"></span>
						</div>
						<ClockIcon v-else class="w-4 h-4 mr-0.5 align-middle" />
						<span v-if="game.statusText != 'TBD'" class="mr-2">
							{{ game.statusText }}
						</span>
						<span v-else-if="game.elapsed">
							{{ game.elapsed }}'
						</span>
						<span v-else>{{ $t("Live now") }}</span>
					</div>
					<span
						class="text-red-500"
						v-else-if="game.hasEnded || game.stateEnded">
						{{ $t("Event Ended") }}
					</span>
					<div v-else class="flex items-center">
						<ClockIcon
							v-if="game.isToday"
							class="w-3 h-3 mr-1 align-middle" />
						<Calendar
							:icon="RiCalendarEventFill"
							v-else
							class="w-3 h-3 mr-1 align-middle" />
						<UseTimeAgo
							:time="
								DateTime.fromSeconds(
									game.startTimeTs,
								).toJSDate()
							"
							v-slot="{ timeAgo }">
							<div>{{ timeAgo }}</div>
						</UseTimeAgo>
					</div>
				</div>
				<div
					class="text-gray-500 ml-2 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					{{ $t("Fill:") }}
					<span
						class="text-sky-600 dark:text-sky-500 whitespace-nowrap">
						<MoneyFormat :amount="stake.filled" />
					</span>
				</div>
				<div
					class="text-gray-500 ml-2 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					{{ $t("Unfill:") }}
					<span
						class="text-sky-600 dark:text-sky-500 whitespace-nowrap">
						<MoneyFormat :amount="stake.unfilled" />
					</span>
				</div>
			</div>
		</div>
		<div class="flex mt-2 items-center justify-between">
			<div v-if="showActions" class="flex items-center space-x-3">
				<PrimaryButton
					v-if="stake.filled > 0"
					secondary
					link
					:href="route('stakes.tradeout.show', { stake: stake.uid })"
					class="!py-0.5 text-xs uppercase">
					<Loading
						v-if="tradeOutForm.processing"
						class="!w-4 !h-4 mr-2 -ml-1" />
					{{ $t("TradeOut") }}
				</PrimaryButton>
				<PrimaryButton
					v-if="stake.unfilled > 0 && stake.isExposed"
					@click.prevent="cancelBet"
					error
					:disabled="cancelForm.processing"
					class="!py-0.5 text-xs uppercase">
					<Loading
						v-if="cancelForm.processing"
						class="!w-4 !h-4 mr-2 -ml-1" />
					{{ $t("Cancel") }}
				</PrimaryButton>
			</div>
			<StatusBadge :status="stake.status" />
		</div>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
