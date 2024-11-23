<script setup>
	import { onMounted, onUnmounted, ref } from "vue";

	import { UseTimeAgo } from "@vueuse/components";
	import { DateTime } from "luxon";
	import { MdAccesstime, RiCalendarEventFill } from "oh-vue-icons/icons";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import Baseball from "@/Layouts/FontendLayout/IconsGray/Baseball.vue";
	import BasketBall from "@/Layouts/FontendLayout/IconsGray/BasketBall.vue";
	import FormularOne from "@/Layouts/FontendLayout/IconsGray/FormularOne.vue";
	import HandBall from "@/Layouts/FontendLayout/IconsGray/HandBall.vue";
	import Hockey from "@/Layouts/FontendLayout/IconsGray/Hockey.vue";
	import NbaIcon from "@/Layouts/FontendLayout/IconsGray/Nba.vue";
	import NflIcon from "@/Layouts/FontendLayout/IconsGray/Nfl.vue";
	import Rugby from "@/Layouts/FontendLayout/IconsGray/Rugby.vue";
	import Soccer from "@/Layouts/FontendLayout/IconsGray/Soccer.vue";
	import VolleyBall from "@/Layouts/FontendLayout/IconsGray/VolleyBall.vue";

	const icons = {
		football: Soccer,
		nfl: NflIcon,
		basketball: BasketBall,
		baseball: Baseball,
		nba: NbaIcon,
		rugby: Rugby,
		volleyball: VolleyBall,
		handball: HandBall,
		hockey: Hockey,
		"formular-one": FormularOne,
	};
	const props = defineProps({
		game: Object,
	});
	const game = ref(props.game);
	const listner = ref();
	onMounted(() => {
		if (props.game?.uuid)
			listner.value = window.Echo.channel(props.game?.uuid).listen(
				"GameUpdated",
				(event) => {
					game.value = event;
				},
			);
	});

	onUnmounted(() => {
		listner.value.stopListening("GameUpdated");
	});
</script>

<template>
	<a
		:href="route('sports.show', { game: game.slug })"
		class="flex items-center bg-white content-contain dark:bg-gray-800 border-t hover:border-t-0 first:border-t-0 border-gray-200 dark:border-gray-700 p-2 transition-colors duration-300 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-700 no-underline">
		<component
			:is="icons[game.sport]"
			class="w-7 h-7 mt-1 mr-3 text-gray-500 opacity-50" />
		<div class="flex-1 block">
			<div
				:class="{
					'flex items-center justify-between': game.hasStarted,
				}">
				<template v-if="game.hasStarted">
					<div>
						<div class="font-bold font-inter flex justify-between">
							{{ game.homeTeam.name }}
						</div>
						<div class="font-bold font-inter">
							{{ game.awayTeam.name }}
						</div>
					</div>
					<div class="ml-2 h-9 -mt-1 flex text-bold text-center">
						<div
							class="flex flex-col gap-y-0.5 flex-nowrap ml-1 relative align-middle">
							<div
								:class="{
									'bg-green-500 text-white':
										game.homeScore > game.awayScore,
									'bg-red-500 text-white':
										game.homeScore < game.awayScore,
									'bg-gray-500 text-white':
										game.homeScore == game.awayScore,
								}"
								class="text-xs py-0 rounded-sm px-1 text-center whitespace-nowrap font-bold">
								{{ game.homeScore }}
							</div>

							<div
								:class="{
									'bg-green-500 text-white':
										game.homeScore < game.awayScore,
									'bg-red-500 text-white':
										game.homeScore > game.awayScore,
									'bg-gray-500 text-white':
										game.homeScore == game.awayScore,
								}"
								class="text-xs py-0 rounded-sm px-1 text-center whitespace-nowrap font-bold">
								{{ game.awayScore }}
							</div>
						</div>
					</div>
				</template>
				<div
					v-else
					class="text-sm font-bold font-inter text-gray-800 dark:text-gray-100">
					<span v-if="game.isSoon">
						{{ game.startsAt }}{{ " - " }}
					</span>
					{{ game.name }}
				</div>
			</div>
			<div
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
						{{ $t("Event Ended") }}
					</span>
					<div v-else class="flex items-center">
						<VueIcon
							:icon="MdAccesstime"
							v-if="game.isToday"
							class="w-3 h-3 mr-1 align-middle" />
						<VueIcon
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
					class="text-gray-500 ml-5 inline-block leading-[1em] text-[10px] font-inter font-extrabold tracking-[0.7px] uppercase">
					{{ $t("Traded:") }}
					<span
						class="text-emerald-600 dark:text-emerald-500 whitespace-nowrap">
						<MoneyFormat :amount="game.traded" />
					</span>
				</div>
			</div>
		</div>
	</a>
</template>
