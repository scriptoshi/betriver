<script setup>
	import { computed, ref } from "vue";

	import { Head, Link, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import {
		HiPencil,
		HiSolidChevronDown,
		HiSolidX,
		HiTrash,
	} from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Flag from "@/Components/Flag";
	import Loading from "@/Components/Loading.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import NoItems from "@/Components/NoItems.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { countries } from "@/constants/countries";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import ApiGamesLoader from "@/Pages/Admin/Games/ApiGamesLoader.vue";

	const props = defineProps({
		games: Object,
		statuses: Object,
		sport: String,
		title: String,
		league: String,
		leagues: Array,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteGameForm = useForm({});
	const gameBeingDeleted = ref(null);

	const deleteGame = () => {
		deleteGameForm.delete(
			window.route("admin.games.destroy", gameBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (gameBeingDeleted.value = null),
			},
		);
	};
	const hasOdds = computed({
		set: (val) => (params.odds = val ? 1 : null),
		get: () => !!params.odds,
	});
	const hasScores = computed({
		set: (val) => (params.scores = val ? 1 : null),
		get: () => !!params.scores,
	});
	debouncedWatch(
		[
			search,
			() => params.bets,
			() => params.day,
			() => params.status,
			() => params.lid,
			() => params.country,
			() => params.odds,
			() => params.scores,
		],
		([search, bets, day, status, lid, country, odds, scores]) => {
			router.get(
				window.route("admin.games.index", {
					sport: props.sport.toLowerCase(),
				}),
				{
					...(search ? { search } : {}),
					...(bets ? { bets } : {}),
					...(day ? { day } : {}),
					...(status ? { status } : {}),
					...(country ? { country } : {}),
					...(odds ? { odds } : {}),
					...(scores ? { scores } : {}),
					...(lid ? { lid } : {}),
				},
				{
					preserveState: true,
					preserveScroll: true,
				},
			);
		},
		{
			maxWait: 700,
		},
	);

	const toggle = (game) => {
		game.busy = true;
		router.put(
			window.route("admin.games.toggle", game.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					game.busy = false;
					gameBeingDeleted.value = null;
				},
			},
		);
	};
	const dateFilterOptions = [
		{ label: "Today", value: "today" },
		{ label: "Tomorrow", value: "tomorrow" },
		{ label: "In 2 days", value: "2-days" },
		{ label: "In 3 days", value: "3-days" },
		{ label: "In 4 days", value: "4-days" },
		{ label: "In 5 days", value: "5-days" },
		{ label: "In 6 days", value: "6-days" },
		{ label: "In 1 week", value: "1-week" },
		{ label: "Yesterday", value: "yesterday" },
		{ label: "2 days ago", value: "2-days-ago" },
		{ label: "3 days ago", value: "3-days-ago" },
		{ label: "4 days ago", value: "4-days-ago" },
		{ label: "5 days ago", value: "5-days-ago" },
		{ label: "6 days ago", value: "6-days-ago" },
		{ label: "1 week ago", value: "1-week-ago" },
	];

	const betsFilterOptions = [
		{ label: "Has any bets", value: "any" },
		{ label: "Has pending bets", value: "pending" },
		{ label: "Has winning bets", value: "wins" },
		{ label: "Has Losing bets", value: "loss" },
		{ label: "Has Bookie bet", value: "bookie" },
		{ label: "Has Bookie Gains", value: "bookie-wins" },
		{ label: "Has Bookie Loss", value: "bookie-lost" },
		{ label: "Bookie Admin Profit", value: "bookie-lost" },
	];
</script>
<template>
	<Head :title="title ?? 'Games'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ sport }} {{ $t("Games") }}</h3>
							<p>{{ $t("Available Games") }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<SearchInput
								class="lg:max-w-[220px] w-full"
								v-model="search" />
							<PrimaryButton
								link
								primary
								:href="
									route(
										'admin.games.create',
										sport.toLowerCase(),
									)
								">
								{{ $t("Create a Game") }}
							</PrimaryButton>
						</div>
					</div>
					<ApiGamesLoader
						v-if="!['ALL', 'Racing'].includes(sport)"
						:sport="sport" />
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="flex flex-col lg:flex-row items-center gap-4 justify-end mb-4 px-6">
								<span
									v-if="league"
									class="badge py-1 px-2 bg-gray-100 dark:bg-gray-900 rounded-3xl text-xs"
									href="#">
									<span>
										{{ league }}
										<a
											href="#"
											@click.prevent="params.lid = null"
											class="p-[1px] group leading-[0] ml-1 bg-white dark:bg-gray-700 rounded-full">
											<VueIcon
												class="w-3 h-3 text-gray-900 dark:text-gray-300 group-hover:text-red-500"
												:icon="HiSolidX" />
										</a>
									</span>
								</span>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										class="md"
										:options="betsFilterOptions"
										valueProp="value"
										label="label"
										:placeholder="$t('Bet Status')"
										v-model="params.bets"
										closeOnSelect>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
										<template #clear="{ clear }">
											<VueIcon
												@click="clear"
												class="mr-1 relative z-10 opacity-60 w-5 h-5"
												:icon="HiSolidX" />
										</template>
									</Multiselect>
								</div>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										class="md"
										:options="dateFilterOptions"
										valueProp="value"
										label="label"
										:placeholder="$t('Filter by Day')"
										v-model="params.day"
										closeOnSelect>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
										<template #clear="{ clear }">
											<VueIcon
												@click="clear"
												class="mr-1 relative z-10 opacity-60 w-5 h-5"
												:icon="HiSolidX" />
										</template>
									</Multiselect>
								</div>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										class="md"
										:options="leagues"
										valueProp="value"
										label="label"
										:placeholder="$t('Filter by League')"
										v-model="params.lid"
										searchable
										closeOnSelect>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
										<template #clear="{ clear }">
											<VueIcon
												@click="clear"
												class="mr-1 relative z-10 opacity-60 w-5 h-5"
												:icon="HiSolidX" />
										</template>
									</Multiselect>
								</div>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										class="md"
										:options="statuses"
										valueProp="value"
										label="label"
										:placeholder="$t('Filter by status')"
										v-model="params.status"
										searchable
										closeOnSelect>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
										<template #clear="{ clear }">
											<VueIcon
												@click="clear"
												class="mr-1 relative z-10 opacity-60 w-5 h-5"
												:icon="HiSolidX" />
										</template>
									</Multiselect>
								</div>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										:options="countries"
										valueProp="value"
										label="label"
										:placeholder="$t('Select a Country')"
										v-model="params.country"
										searchable
										class="md"
										closeOnSelect>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
										<template #clear="{ clear }">
											<a
												href="#"
												class="p-0.5 relative z-10 opacity-60 flex-shrink-0 flex-grow-0"
												@click.prevent="clear">
												<VueIcon
													@click="clear"
													class="mr-1 w-4 h-4"
													:icon="HiSolidX" />
											</a>
										</template>
									</Multiselect>
								</div>
							</div>

							<NoItems
								v-if="games.data.length == 0"
								class="border-t dark:border-gray-600">
								No {{ sport == "all" ? "" : sport }} Games Found
							</NoItems>
							<div v-else>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table">
										<thead>
											<tr role="row">
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Game") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													<Switch v-model="hasOdds">
														{{ "Odds" }}
													</Switch>
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													<Switch v-model="hasScores">
														{{ $t("Scores") }}
													</Switch>
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Start") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Status") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Betting") }}
												</th>

												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="game in games.data"
												:key="game.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-3">
														<div
															class="flex items-center -space-x-2">
															<img
																class="w-7 h-7 rounded-full border-2 border-gray-500"
																:src="
																	game
																		.homeTeam
																		.image
																" />
															<img
																class="w-7 h-7 rounded-full border-2 border-gray-500"
																:src="
																	game
																		.awayTeam
																		.image
																" />
														</div>
														<div>
															<p
																v-tippy="
																	game.name
																"
																class="max-w-[220px] cursor-pointer truncate text-ellipsis">
																{{ game.name }}
															</p>
															<div
																class="flex items-center">
																<Flag
																	class="mr-2 w-4 h-auto"
																	v-if="
																		game
																			.league
																			.country &&
																		game
																			.league
																			.country
																			.length ==
																			2
																	"
																	:iso="
																		game
																			.league
																			.country
																	" />
																<p
																	class="text-xs font-semibold">
																	{{
																		game.sport
																	}}
																	|
																	<a
																		href="#"
																		v-tippy="
																			$t(
																				'Filter by League',
																			)
																		"
																		@click.prevent="
																			params.lid =
																				game.league_id
																		"
																		class="text-sky-500 hover:text-sky-600 dark:hover:text-sky-400">
																		<div
																			class="max-w-[150px] inline-flex truncate text-ellipsis">
																			{{
																				game
																					.league
																					.name
																			}}
																		</div>
																	</a>
																</p>
															</div>
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-2">
														<Link
															v-tippy="
																game.has_odds
																	? $t(
																			'Manage Game Odds',
																	  )
																	: $t(
																			'Create Game Odds',
																	  )
															"
															:class="
																game.has_odds
																	? 'text-green-500 hover:text-green-600 dark:hover:text-green-400'
																	: 'text-amber-500 hover:text-amber-600 dark:hover:text-amber-400'
															"
															:href="
																route(
																	'admin.odds.index',
																	game.uuid,
																)
															">
															Odds & Markets
														</Link>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Link
														v-tippy="
															game.has_scores
																? $t(
																		'Manage Game Scores',
																  )
																: $t(
																		'Provide Game Scores',
																  )
														"
														:class="
															game.has_scores
																? 'text-green-500 hover:text-green-600 dark:hover:text-green-400'
																: 'text-amber-500 hover:text-amber-600 dark:hover:text-amber-400'
														"
														:href="
															route(
																'admin.scores.index',
																game.uuid,
															)
														">
														Scores
													</Link>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													{{ game.startTimeAgo }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													{{ game.statusText }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Switch
														:modelValue="
															game.active
														"
														@update:modelValue="
															toggle(game)
														">
														<template
															v-if="game.active">
															{{ "Active" }}
														</template>
														<template v-else>
															{{ "Disabled" }}
														</template>
													</Switch>
												</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<Link
															:href="
																route(
																	'admin.games.edit',
																	game.id,
																)
															"
															class="cursor-pointer p-2 hover:text-blue-600">
															<VueIcon
																:icon="HiPencil"
																class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																gameBeingDeleted =
																	game
															"
															class="cursor-pointer p-2 hover:text-red-500">
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="games.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="gameBeingDeleted"
			@close="gameBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {game} ?", {
						game: gameBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the game from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{ $t("Its Recommended to Disable the game Instead") }}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="gameBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="gameBeingDeleted.active"
					@click="toggle(gameBeingDeleted)">
					<Loading v-if="gameBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteGame"
					:class="{ 'opacity-25': deleteGameForm.processing }"
					:disabled="deleteGameForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
