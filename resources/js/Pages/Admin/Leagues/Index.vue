<script setup>
	import { ref } from "vue";

	import { Head, Link, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiPencil, HiSolidPlus, HiTrash } from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Loading from "@/Components/Loading.vue";
	import NoItems from "@/Components/NoItems.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
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
		[search],
		([search]) => {
			router.get(
				window.route("admin.leagues.index"),
				{ ...(search ? { search } : {}) },
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
					leagueBeingDeleted.value = null;
				},
			},
		);
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
							<h3 class="h3">
								{{ $t("Accepted Leagues") }}
							</h3>
							<p>{{ $t("Available Leagues") }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
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
								Create New League
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-end mb-4 px-6">
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
									<SearchInput
										class="max-w-md"
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
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Description") }}
												</th>

												<td role="columnheader"></td>
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
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="max-w-xs truncate text-ellipsis">
														{{ league.description }}
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
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
