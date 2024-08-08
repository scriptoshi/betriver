<script setup>
	import { ref } from "vue";

	import { Head, Link, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import {
		HiPencil,
		HiSolidChevronDown,
		HiSolidX,
		HiTrash,
	} from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Loading from "@/Components/Loading.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import NoItems from "@/Components/NoItems.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		games: Object,
		statuses: Object,
		sport: String,
		title: String,
		league: String,
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
	debouncedWatch(
		[search, () => params.status, () => params.lid],
		([search, status, lid]) => {
			router.get(
				window.route("admin.games.index"),
				{
					...(search ? { search } : {}),
					...(status ? { status } : {}),
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
								<div class="lg:max-w-[284px] w-full">
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
								<SearchInput
									class="lg:max-w-[284px] w-full"
									v-model="search" />
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
													{{ $t("Odds") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Scores") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Start time") }}
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
															<p>
																{{ game.name }}
															</p>
															<p
																class="text-xs font-semibold">
																{{ game.sport }}
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
																	{{
																		game
																			.league
																			.name
																	}}
																</a>
															</p>
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Link
														class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400"
														:href="
															route(
																'admin.odds.index',
																game.uuid,
															)
														">
														Manage Odds
													</Link>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Link
														class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400"
														:href="
															route(
																'admin.scores.index',
																game.uuid,
															)
														">
														Manage Scores
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
