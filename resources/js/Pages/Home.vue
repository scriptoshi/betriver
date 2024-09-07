<script setup>
	import { computed } from "vue";

	import { Head } from "@inertiajs/vue3";

	import EventCard from "@/Components/Cards/EventCard.vue";
	import GameRow from "@/Components/Cards/GameRow.vue";
	import HomeEventCard from "@/Components/Cards/HomeEventCard.vue";
	import HomePageCarousel from "@/Components/Carousel/HomePageCarousel.vue";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import BettingSideBar from "@/Pages/Games/BettingSideBar.vue";
	const props = defineProps({
		popular: Array,
		slides: Array,
		games: Array,
		top: Array,
		enableExchange: Boolean,
		enableBookie: Boolean,
		defaultMarket: Object,
		multiples: Boolean,
	});

	const showBookie = computed(() => {
		if (!props.enableBookie) return false;
		return props.multiples;
	});
	const showExchange = computed(() => {
		if (!props.enableExchange) return false;
		return !props.multiples;
	});
</script>

<template>
	<Head title="Welcome" />
	<AppLayout>
		<div class="px-3">
			<div class="pb-4 pt-3">
				<HomePageCarousel :slides="slides" />
			</div>
			<div class="grid gap-3 mb-12">
				<div>
					<h3 class="text-gray-900 font-inter dark:text-white">
						{{ $t("Top Football") }}
					</h3>
				</div>
				<div class="grid gap-3">
					<GameRow
						v-for="game in games"
						:key="game.slug"
						:defaultMarketsCount="defaultMarketsCount"
						:market="defaultMarket"
						:showBookie="showBookie"
						:showExchange="showExchange"
						:game="game" />
				</div>
				<div>
					<h3 class="text-gray-900 font-inter dark:text-white">
						{{ $t("Top Markets") }}
					</h3>
				</div>
				<div class="grid gap-4 sm:grid-cols-2">
					<HomeEventCard
						:game="topgame"
						:showBookie="showBookie"
						:defaultMarket="defaultMarket"
						v-for="topgame in top"
						:key="topgame.id" />
				</div>
			</div>
		</div>
		<template #right-sidebar>
			<div>
				<BettingSideBar :multiples="multiples" />
				<div
					class="bg-gray-300 text-gray-900 dark:text-white dark:bg-gray-750 border-b border-gray-250 dark:border-gray-850 flex items-center px-2.5 uppercase font-inter text-sm tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
					Top Events
				</div>
				<div class="grid">
					<EventCard
						v-for="game in popular"
						:key="game.slug"
						:game="game" />
				</div>
			</div>
		</template>
	</AppLayout>
</template>
