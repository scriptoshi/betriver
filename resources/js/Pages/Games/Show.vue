<script setup>
	import { computed, ref } from "vue";

	import { Head, usePage } from "@inertiajs/vue3";

	import BreadCrumbs from "@/Components/BreadCrumbs.vue";
	import ContractsCard from "@/Components/Cards/ContractsCard.vue";
	import EventCard from "@/Components/Cards/EventCard.vue";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import MenuLink from "@/Pages/Account/Settings/MenuLink.vue";
	import BettingSideBar from "@/Pages/Games/BettingSideBar.vue";
	import EventInfo from "@/Pages/Games/EventInfo.vue";
	const props = defineProps({
		game: Object,
		markets: Array,
		popular: Array,
		overunders: Object,
		handicaps: Object,
		asianhandicaps: Object,
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
	const multiples = computed(() => usePage().props.multiples);
	const showBookie = computed(() => {
		if (!props.enableBookie) return false;
		return multiples.value;
	});
	const showExchange = computed(() => {
		if (!props.enableExchange) return false;
		return !multiples.value;
	});

	const filteredMarkets = computed(() => {
		if (filter.value === "all") return props.markets;
		if (filter.value === "popular")
			return props.markets
				.slice()
				.sort(
					(a, b) =>
						parseFloat(b.traded ?? 0) - parseFloat(a.traded ?? 0),
				);
		return props.markets.filter((m) => m.category === filter.value);
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
				<EventInfo :game="game" />
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
					v-for="(market, index) in filteredMarkets"
					:key="market.uuid"
					:market="market"
					:game="game"
					:opened="index == 0"
					:showBookie="showBookie"
					:showExchange="showExchange"
					:handicaps="
						market.slug.includes('handicap')
							? market.slug.includes('asian')
								? asianhandicaps
								: handicaps
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
