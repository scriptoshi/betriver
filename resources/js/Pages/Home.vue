<script setup>
	import AppLayout from "@/Layouts/AppLayout.vue";
	import PopularEvents from "@/Pages/Games/Show/PopularEvents.vue";
	import Category from "@/Pages/Home/Category.vue";
	import LargeMarketCard from "@/Pages/Home/LargeMarketCard.vue";
	import TopMarket from "@/Pages/Home/TopMarket.vue";
	import TopPromos from "@/Pages/Home/TopPromos.vue";
	import FixtureCard from "@/Pages/Sports/Index/FixtureCard.vue";
	defineProps({
		games: Object,
		sport: String,
		country: String,
		league: String,
		leagues: Array,
		popular: Array,
		popularGames: Array,
		liveGames: Array,
		soonGames: Array,
	});

	const categories = [
		{
			name: "Bundesliga",
			section: "Soccer",
			id: "bundesliga",
			image: "https://cdn.betn.io/cat/budesliga.jpg",
			url: window.route("sports.index", {
				sport: "soccer",
				country: "this-week",
				league: "bundesliga",
			}),
			events: `10+`,
		},
		{
			name: "Premier League",
			section: "Soccer",
			id: "premier-league",
			image: "https://cdn.betn.io/cat/premiere.jpg",
			url: window.route("sports.index", {
				sport: "soccer",
				country: "this-week",
				league: "premier-league",
			}),
			events: `20+`,
		},
		{
			name: "Spanish Liga",
			section: "Soccer",
			id: "la-liga",
			image: "https://cdn.betn.io/cat/liga.jpg",
			url: window.route("sports.index", {
				sport: "soccer",
				country: "this-week",
				league: "la-liga",
			}),
			events: `18+`,
		},
		{
			name: "Championship",
			section: "Soccer",
			id: "championship",
			image: "https://cdn.betn.io/cat/fa.jpg",
			url: window.route("sports.index", {
				sport: "soccer",
				country: "this-week",
				league: "championship",
			}),
			events: `6+`,
		},
	];
</script>
<template>
	<AppLayout>
		<main class="h-full">
			<div class="mt-8 container-fluid">
				<section>
					<div
						class="grid relative grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
						<div class="lg:col-span-2">
							<div class="w-full">
								<TopPromos />
							</div>
							<h3 class="my-6">Starting Soon</h3>
							<div class="grid gap-3 w-full">
								<FixtureCard
									v-for="game in games"
									:key="game.id"
									:game="game" />
							</div>
							<h3 class="my-6">Top Categories this week</h3>
							<div class="grid grid-cols-4 mt-5 gap-3 w-full">
								<Category
									v-for="category in categories"
									:key="category.id"
									:category="category" />
							</div>
							<h3 class="my-6">Open markets</h3>
							<div class="grid grid-cols-2 gap-3 w-full">
								<template
									v-for="(game, i) in soonGames"
									:key="game.id">
									<TopMarket v-if="i != 2" :game="game" />
									<LargeMarketCard
										v-if="i == 2"
										:game="game"
										class="row-span-2" />
								</template>
							</div>
						</div>
						<div
							class="dark:bg-gray-800 h-[calc(100vh-40px)] bg-white/90 sticky top-16">
							<template v-if="liveGames.length > 0">
								<div
									class="bg-gray-300/40 dark:bg-gray-700/40 text-gray-900 dark:text-white p-3 text-sm font-semibold">
									{{ $t("Live Games") }}
								</div>
								<PopularEvents :games="liveGames" />
								<div class="p-4 bg-gray-100 dark:bg-gray-900" />
							</template>

							<div
								class="bg-gray-300/40 dark:bg-gray-700/40 text-gray-900 dark:text-white p-3 text-sm font-semibold">
								{{ $t("Popular Games") }}
							</div>
							<PopularEvents
								class="!mt-0"
								:games="popularGames" />
						</div>
					</div>
				</section>
			</div>
		</main>
	</AppLayout>
</template>
