<script setup>
	import { computed } from "vue";

	import { Head } from "@inertiajs/vue3";

	import BreadCrumbs from "@/Components/BreadCrumbs.vue";
	import EventCard from "@/Components/Cards/EventCard.vue";
	import GameRow from "@/Components/Cards/GameRow.vue";
	import Pagination from "@/Components/Pagination.vue";
	import Switch from "@/Components/Switch.vue";
	import { toTitleCase } from "@/Layouts/FontendLayout/useMenu";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import BettingSideBar from "@/Pages/Games/BettingSideBar.vue";
	const props = defineProps({
		league: Object,
		sport: String,
		region: String,
		popular: Array,
		games: Object,
		defaultMarketsCount: Number,
		defaultMarket: Object,
		enableExchange: Boolean,
		enableBookie: Boolean,
	});
	const crumbs = [
		{ name: "Home", href: "/" },
		{ name: props.league?.name, href: null },
	];
	/**
	 * off on the homepage for now.
	 */
	const multiples = false;
	const showBookie = computed(() => {
		if (!props.enableBookie) return false;
		return multiples.value;
	});
	const showExchange = computed(() => {
		if (!props.enableExchange) return false;
		return !multiples.value;
	});
</script>

<template>
	<Head title="Dashboard" />

	<AppLayout>
		<div class="px-3.5">
			<div class="flex justify-between">
				<BreadCrumbs class="mt-3" :crumbs="crumbs" />
				<Switch class="mt-4 hidden" v-model="multiples">
					<h3 class="text-sm uppercase font-inter">Multiples</h3>
				</Switch>
			</div>
			<div class="grid pb-6 pt-3">
				<h1
					v-if="league?.name"
					class="text-lg sm:text-3xl md:text-4xl text-gray-850 dark:text-white font-inter font-extrabold">
					{{ toTitleCase(league?.name) }}
				</h1>
				<h1
					v-else
					class="text-lg sm:text-3xl md:text-4xl text-gray-850 dark:text-white font-inter font-extrabold">
					{{ toTitleCase(sport ?? "Sports") }}
					{{ toTitleCase(region ?? "") }}
				</h1>
			</div>
			<div class="grid gap-3">
				<GameRow
					v-for="game in games.data"
					:key="game.slug"
					:defaultMarketsCount="defaultMarketsCount"
					:market="defaultMarket"
					:showBookie="showBookie"
					:showExchange="showExchange"
					:game="game" />
				<Pagination :meta="games.meta" />
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
