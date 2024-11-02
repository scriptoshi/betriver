<script setup>
	import { computed } from "vue";

	import { Head, usePage } from "@inertiajs/vue3";

	import BreadCrumbs from "@/Components/BreadCrumbs.vue";
	import GameRow from "@/Components/Cards/GameRow.vue";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import BettingSideBar from "@/Pages/Games/BettingSideBar.vue";
	const props = defineProps({
		league: Object,
		sport: String,
		region: String,
		popular: Array,
		games: Object,
		defaultMarketsCounts: Array,
		defaultMarkets: Object,
		enableExchange: Boolean,
		enableBookie: Boolean,
	});
	const crumbs = [
		{ name: "Home", href: "/" },
		{ name: "watchlist", href: null },
	];
	/**
	 * off on the homepage for now.
	 */
	const multiples = computed(() => usePage().props.multiples);
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
			</div>
			<div class="grid pb-6 pt-3">
				<h1
					class="text-lg sm:text-3xl md:text-4xl text-gray-850 dark:text-white font-inter font-extrabold">
					{{ $t("Watchlist") }}
				</h1>
			</div>
			<div class="grid gap-3">
				<template v-for="game in games" :key="game.slug">
					<GameRow
						:defaultMarketsCount="defaultMarketsCounts[game.sport]"
						:market="defaultMarkets[game.sport]"
						:showBookie="showBookie"
						:showExchange="showExchange"
						:game="game" />
				</template>
			</div>
		</div>
		<template #right-sidebar-top>
			<BettingSideBar :multiples="multiples" />
		</template>
	</AppLayout>
</template>
