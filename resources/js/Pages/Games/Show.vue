<script setup>
	import { computed, ref } from "vue";

	import { Head } from "@inertiajs/vue3";
	import { useLocalStorage } from "@vueuse/core";

	import BreadCrumbs from "@/Components/BreadCrumbs.vue";
	import ContractsCard from "@/Components/Cards/ContractsCard.vue";
	import EventCard from "@/Components/Cards/EventCard.vue";
	import Switch from "@/Components/Switch.vue";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import MenuLink from "@/Pages/Account/Settings/MenuLink.vue";
	import BettingSideBar from "@/Pages/Games/BettingSideBar.vue";
	import EventInfo from "@/Pages/Games/EventInfo.vue";
	const props = defineProps({
		game: Object,
		markets: Object,
		popular: Array,
		overunders: Object,
		handicaps: Object,
		enableExchange: Boolean,
		enableBookie: Boolean,
	});
	const crumbs = [
		{ name: "Home", href: "/" },
		{
			name: props.game.league?.name,
			href: window.route("sports.index", {
				sport: props.game.sport,
				region: props.game.league?.slug,
			}),
		},
		{
			name: props.game?.name,
			href: null,
		},
	];
	const filter = ref("all");
	const filters = [
		"all",
		"popular",
		"winner",
		"teams",
		"totals",
		"handicap",
		"half",
	];
	const multiples = useLocalStorage("multiples", false);
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
		<div class="sm:px-3.5">
			<div class="px-2 sm:px-0">
				<BreadCrumbs class="mt-3" :crumbs="crumbs" />
			</div>
			<div class="grid px-2 sm:px-0 pb-6 pt-3">
				<EventInfo :game="game">
					<template #multiples>
						<Switch v-model="multiples">
							<h3 class="text-sm uppercase font-inter">
								Multiples
							</h3>
						</Switch>
					</template>
				</EventInfo>
				<div
					class="my-2 pt-2 border-t border-gray-250 dark:border-gray-750 flex items-center space-x-4 overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-track-transparent">
					<MenuLink
						v-for="item in filters"
						:key="item"
						:active="filter === item"
						@click="filter = item"
						btn>
						{{ item }}
					</MenuLink>
				</div>
			</div>
			<div class="grid gap-3">
				<ContractsCard
					v-for="(market, index) in markets"
					:key="market.uuid"
					:market="market"
					:game="game"
					:opened="index == 0"
					:showBookie="showBookie"
					:showExchange="showExchange"
					v-show="(showBookie && market.has_odds) || showExchange"
					:handicaps="
						market.slug.includes('handicap')
							? handicaps
							: market.slug.includes('overunder')
							? overunders
							: []
					" />
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
