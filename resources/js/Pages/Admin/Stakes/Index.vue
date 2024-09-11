<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { ChevronsRight } from "lucide-vue-next";
	import { HiSolidChevronDown, HiSolidX } from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Loading from "@/Components/Loading.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { ucfirst } from "@/hooks";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		stakes: Object,
		filter: String,
		title: { required: false, type: String },
		statuses: Object,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteStakeForm = useForm({});
	const stakeBeingDeleted = ref(null);

	const deleteStake = () => {
		deleteStakeForm.delete(
			window.route("admin.stakes.destroy", stakeBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (stakeBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search, () => params.status],
		([search, status]) => {
			router.get(
				window.route("admin.stakes.index"),
				{
					...(search ? { search } : {}),
					...(status ? { status } : {}),
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
</script>
<template>
	<Head :title="title ?? 'Stakes'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ filter ? ucfirst(filter) : $t("All") }}
								{{ $t("Stakes") }}
							</h3>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex gap-3 items-center justify-end mb-4 px-6">
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
									<SearchInput
										class="max-w-md"
										v-model="search" />
								</div>
								<div class="lg:max-w-[200px] w-full">
									<Multiselect
										class="md"
										:options="Object.values(statuses)"
										valueProp="value"
										label="name"
										:placeholder="$t('Filter Status')"
										v-model="params.status"
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
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table">
										<thead>
											<tr role="row">
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("User") }} &
													{{ $t("Game") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Bet") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Staked") }}/{{
														$t("Payout")
													}}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Filled") }}/{{
														$t("Unfilled")
													}}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Status") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Won") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Claimed") }}
												</th>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="stake in stakes.data"
												:key="stake.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div class="underline">
															{{
																stake.user.email
															}}
														</div>
														<div>
															{{
																stake.game_info
															}}
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div>
															{{
																stake.market_info
															}}
														</div>
														<div>
															<span
																:class="
																	stake.isLay
																		? 'text-sky-500'
																		: 'text-emerald-500'
																">
																{{
																	stake.isLay
																		? "Against"
																		: "For"
																}}
															</span>
															{{ stake.bet_info }}
														</div>
													</div>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div
															class="flex items-center">
															<MoneyFormat
																:amount="
																	stake.amount
																" />
															<ChevronsRight
																class="w-4 h-4 mx-0.5 text-emerald-500" />

															<MoneyFormat
																:amount="
																	stake.payout
																" />
														</div>
														<div
															class="uppercase text-xs">
															<span
																class="text-emerald-500">
																{{ $t("Odds") }}
																:
															</span>
															<OddsFormat
																:odds="
																	stake.odds
																" />
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<MoneyFormat
														:amount="
															stake.filled
														" />
													<span
														class="text-emerald-500">
														/
													</span>
													<MoneyFormat
														:amount="
															stake.unfilled
														" />
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<StatusBadge
														:status="
															stake.status
														" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Switch
														disabled
														v-model="stake.won" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Switch
														disabled
														v-model="
															stake.is_withdrawn
														" />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="stakes.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="stakeBeingDeleted"
			@close="stakeBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {stake} ?", {
						stake: stakeBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the stake from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{ $t("Its Recommended to Disable the stake Instead") }}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="stakeBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="stakeBeingDeleted.active"
					@click="toggle(stakeBeingDeleted)">
					<Loading v-if="stakeBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteStake"
					:class="{ 'opacity-25': deleteStakeForm.processing }"
					:disabled="deleteStakeForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
