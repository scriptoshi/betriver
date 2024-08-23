<script setup>
	import { computed, ref } from "vue";

	import { Head, Link, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import {
		HiPencil,
		HiSolidChevronDown,
		HiSolidPlus,
		HiSolidX,
		HiTrash,
		PrCloudDownload,
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
	import OddLoader from "@/Pages/Admin/Leagues/OddLoader.vue";

	const props = defineProps({
		leagues: Object,
		title: String,
		sport: String,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteLeagueForm = useForm({});
	const leagueBeingDeleted = ref(null);

	const deleteLeague = () => {
		deleteLeagueForm.delete(
			window.route("admin.leagues.destroy", leagueBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (leagueBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[
			search,
			() => params.country,
			() => params.active,
			() => params.submenu,
			() => params.odds,
		],
		([search, country, active, submenu, odds]) => {
			router.get(
				window.route("admin.leagues.index", {
					sport: props.sport.toLowerCase(),
				}),
				{
					...(search ? { search } : {}),
					...(country ? { country } : {}),
					...(active ? { active } : {}),
					...(submenu ? { submenu } : {}),
					...(odds ? { odds } : {}),
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

	const toggle = (league) => {
		league.busy = true;
		router.put(
			window.route("admin.leagues.toggle", league.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					league.busy = false;
				},
			},
		);
	};
	const toggleMenu = (league) => {
		league.busy = true;
		router.put(
			window.route("admin.leagues.menu", league.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					league.busy = false;
				},
			},
		);
	};
	const apiForm = useForm({ sport: props.sport.toLowerCase() });
	const pullFromApi = (league) => {
		apiForm.post(window.route("admin.leagues.pull"));
	};
	const enableForm = useForm({
		country: null,
		sport: props.sport.toLowerCase(),
	});
	const enableAll = () => {
		enableForm.country = params.country;
		enableForm.put(window.route("admin.leagues.enable"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
	const onlyActive = computed({
		set: (val) => (params.active = val ? 1 : null),
		get: () => !!params.active,
	});
	const hasOdds = computed({
		set: (val) => (params.odds = val ? 1 : null),
		get: () => !!params.odds,
	});
	const disableForm = useForm({
		country: null,
		sport: props.sport.toLowerCase(),
	});
	const disableAll = () => {
		disableForm.country = params.country;
		disableForm.put(window.route("admin.leagues.disable"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
</script>
<template>
	<Head :title="title ?? 'Leagues'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 v-if="sport == 'Politics'" class="h3">
								Manage Political Races
							</h3>
							<h3 v-else class="h3">
								Manage {{ sport == "ALL" ? "" : sport }} Leagues
							</h3>
							<p
								v-if="
									!['ALL', 'Racing', 'Politics'].includes(
										sport,
									)
								">
								{{
									$t(
										"Enable only 10-50 Leagues as to save on your API costs",
									)
								}}
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								secondary
								v-if="
									!['ALL', 'Racing', 'Politics'].includes(
										sport,
									)
								"
								@click.prevent="pullFromApi">
								<Loading v-if="apiForm.processing" />
								<VueIcon
									v-else
									:icon="PrCloudDownload"
									class="mr-2 -ml-1" />
								Load from API
							</PrimaryButton>
							<PrimaryButton
								link
								primary
								:href="
									route(
										'admin.leagues.create',
										sport.toLowerCase(),
									)
								">
								<VueIcon
									:icon="HiSolidPlus"
									class="mr-2 -ml-1" />
								New League
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-between mb-4 px-6">
								<div
									class="flex items-center space-x-3 justify-start">
									<PrimaryButton
										@click.prevent="enableAll"
										class="!py-1">
										<Loading v-if="enableForm.processing" />
										Enable All
									</PrimaryButton>
									<PrimaryButton
										@click.prevent="disableAll"
										error
										class="!py-1">
										<Loading
											v-if="disableForm.processing" />
										Disable All
									</PrimaryButton>
								</div>
								<div
									class="max-w-lg w-full flex items-center space-x-3">
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
									<SearchInput
										class="max-w-xs w-full"
										v-model="search" />
								</div>
							</div>
							<NoItems
								v-if="leagues.data.length == 0"
								class="border-t dark:border-gray-600">
								No
								{{
									$page.props.sport == "ALL"
										? ""
										: $page.props.sport
								}}
								Leagues Found
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
													{{ $t("Name") }}
												</th>
												<th
													scope="col"
													v-if="
														![
															'ALL',
															'Racing',
															'Politics',
														].includes(sport)
													"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													<Switch v-model="hasOdds">
														{{ "Has Odds" }}
													</Switch>
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Season ends") }}
												</th>

												<td role="columnheader">
													<div
														class="flex items-center space-x-3">
														<Switch
															v-model="
																onlyActive
															">
															{{ "Active" }}
														</Switch>
														<Switch
															v-model="
																params.submenu
															">
															{{ "Submenu" }}
														</Switch>
													</div>
												</td>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="league in leagues.data"
												:key="league.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center">
														<img
															class="w-8 h-8 mr-4 rounded-full"
															:src="
																league.image
															" />
														{{ league.name }}
														<Flag
															class="ml-3 w-6 h-auto"
															v-if="
																league.country &&
																league.country
																	.length == 2
															"
															:iso="
																league.country
															" />
													</div>
												</td>
												<td
													v-if="
														![
															'ALL',
															'Racing',
															'Politics',
														].includes(sport)
													"
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-2">
														Odds
														<OddLoader
															:league="league.id"
															:has-odds="
																league.has_odds
															"
															:sport="
																sport.toLowerCase()
															" />
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="max-w-xs truncate text-ellipsis">
														{{ league.season_ends }}
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-3">
														<Switch
															:modelValue="
																league.active
															"
															@update:modelValue="
																toggle(league)
															">
															<template
																v-if="
																	league.active
																">
																{{ "Active" }}
															</template>
															<template v-else>
																{{ "Disabled" }}
															</template>
														</Switch>
														<Switch
															:modelValue="
																league.menu
															"
															@update:modelValue="
																toggleMenu(
																	league,
																)
															">
															{{ $t("Submenu") }}
														</Switch>
													</div>
												</td>

												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<Link
															:href="
																route(
																	'admin.leagues.edit',
																	{
																		league: league.id,
																	},
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
																leagueBeingDeleted =
																	league
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
								<Pagination :meta="leagues.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="leagueBeingDeleted"
			@close="leagueBeingDeleted = null">
			<template #title>
				<p>Are you sure about deleting this league:</p>
				<small>{{ leagueBeingDeleted.name }}</small>
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the league from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{ $t("Its Recommended to Disable the league Instead") }}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="leagueBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="leagueBeingDeleted.active"
					@click="toggle(leagueBeingDeleted)">
					<Loading v-if="leagueBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteLeague"
					:class="{ 'opacity-25': deleteLeagueForm.processing }"
					:disabled="deleteLeagueForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
