<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { uid } from "uid";
	import draggable from "vuedraggable";

	import DialogModal from "@/Components/DialogModal.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	const props = defineProps({
		markets: Object,
		sport: String,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const marketBeingDeleted = ref(null);
	const marketList = ref([...props.markets]);
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.markets.index"),
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

	const toggle = (market) => {
		market.busy = true;
		router.put(
			window.route("admin.markets.toggle", market.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					market.busy = false;
					marketBeingDeleted.value = null;
				},
			},
		);
	};
	const marketBeingReordered = ref();
	const reorderMarketForm = useForm({
		setAll: false,
		market_id: null,
		sequence: null,
	});
	const reorderForm = useForm({ markets: null });
	const renderKey = ref(uid());
	const saveMarketsOrder = () => {
		const order = marketList.value.map((val, key) => ({
			id: val.id,
			sequence: key,
		}));
		reorderForm
			.transform((data) => ({ markets: order }))
			.post(window.route("admin.markets.reorder"), {
				onFinish() {
					reorderForm.reset();
					renderKey.value = uid();
					marketList.value = [...props.markets];
				},
			});
	};
	const reorderMarket = () => {
		reorderMarketForm.market_id = marketBeingReordered.value.id;
		reorderMarketForm.sequence = marketBeingReordered.value.sequence;
		reorderMarketForm.post(window.route("admin.markets.sequence"), {
			onFinish() {
				reorderMarketForm.reset();
				marketBeingReordered.value = null;
				renderKey.value = uid();
				marketList.value = [...props.markets];
			},
		});
	};
</script>
<template>
	<Head :title="title ?? 'Markets'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								Manage {{ sport == "ALL" ? "" : sport }} markets
							</h3>
							<p>
								{{
									$t(
										"Disabled markets will not listed for betting!",
									)
								}}
							</p>
							<p class="text-emerald-600 dark:text-emerald-400">
								{{
									$t(
										"Drag markets to reorder how they show on the frontend",
									)
								}}
							</p>
						</div>
					</div>
					<div
						class="card bg-gray-60 dark:bg-gray-900 border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center space-x-3 justify-end mb-4 px-6">
								<PrimaryButton
									:disabled="reorderForm.processing"
									@click="saveMarketsOrder">
									<Loading
										v-if="reorderForm.processing"
										class="mr-2 -ml-1" />
									Save Markets Order
								</PrimaryButton>
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
									<SearchInput
										class="max-w-md"
										v-model="search" />
								</div>
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover border-separate border-spacing-x-0 border-spacing-y-3"
										role="table">
										<thead class="hidden">
											<tr role="row">
												<th role="columnheader">
													{{ $t("Order") }}
												</th>
												<th role="columnheader">
													{{ $t("Market") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("sport") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Exchange") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Bookie") }}
												</th>
											</tr>
										</thead>
										<tbody
											v-if="sport == 'ALL'"
											role="rowgroup">
											<tr
												v-for="market in markets"
												:key="market.id"
												role="row">
												<td>#{{ market.sequence }}</td>
												<td
													class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
													{{ market.name }}
												</td>
												<td
													class="px-6 py-4 uppercase whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
													{{ market.sport }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
													<Switch
														:modelValue="
															market.active
														"
														@update:modelValue="
															toggle(market)
														">
														<template
															v-if="
																market.active
															">
															{{
																"Exchange Active"
															}}
														</template>
														<template v-else>
															{{
																"Exchange Disabled"
															}}
														</template>
													</Switch>
												</td>
												<td
													class="rounded-r-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
													<Switch
														:modelValue="
															market.active
														"
														@update:modelValue="
															toggle(market)
														">
														<template
															v-if="
																market.active
															">
															{{
																"Bookie Active"
															}}
														</template>
														<template v-else>
															{{
																"Bookie Disabled"
															}}
														</template>
													</Switch>
												</td>
											</tr>
										</tbody>
										<tbody
											is="vue:draggable"
											v-model="marketList"
											tag="tbody"
											item-key="id">
											<template
												#item="{
													element: market,
													index,
												}">
												<tr role="row">
													<td
														class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
														<PrimaryButton
															@click.prevent="
																marketBeingReordered =
																	market
															"
															secondary>
															#{{ index }}
														</PrimaryButton>
													</td>
													<td
														class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
														{{ market.name }}
													</td>
													<td
														class="px-6 py-4 uppercase whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
														{{ market.sport }}
													</td>
													<td
														class="px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
														<Switch
															:modelValue="
																market.active
															"
															@update:modelValue="
																toggle(market)
															">
															<template
																v-if="
																	market.active
																">
																{{
																	"Exchange Active"
																}}
															</template>
															<template v-else>
																{{
																	"Exchange Disabled"
																}}
															</template>
														</Switch>
													</td>
													<td
														class="rounded-r-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
														<Switch
															:modelValue="
																market.active
															"
															@update:modelValue="
																toggle(market)
															">
															<template
																v-if="
																	market.active
																">
																{{
																	"Bookie Active"
																}}
															</template>
															<template v-else>
																{{
																	"Bookie Disabled"
																}}
															</template>
														</Switch>
													</td>
												</tr>
											</template>
										</tbody>
									</table>
								</div>
								<DialogModal
									maxWidth="lg"
									:show="marketBeingReordered"
									@close="marketBeingReordered = null">
									<template #title>
										<h3>Force Order Market</h3>
										<p>{{ marketBeingReordered.name }}</p>
									</template>
									<template #content>
										<p>
											You can enter the position you want
											the market to show at.
										</p>
										<p>
											All items with lower order will be
											displayed first
										</p>
										<FormInput
											label="Item Order"
											class="mt-3"
											v-model="
												marketBeingReordered.sequence
											" />
										<div class="mt-4">
											<Switch
												v-model="
													reorderMarketForm.setAll
												">
												Update All markets to this order
											</Switch>
										</div>
										<div
											class="flex mt-4 space-x-3 items-center justify-end w-full">
											<PrimaryButton
												@click="
													marketBeingReordered = null
												"
												secondary>
												Cancel
											</PrimaryButton>
											<PrimaryButton
												@click="reorderMarket"
												:disabled="
													reorderMarketForm.processing
												"
												primary>
												<Loading
													v-if="
														reorderMarketForm.processing
													"
													class="mr-2 -ml-1" />
												Save Order
											</PrimaryButton>
										</div>
									</template>
								</DialogModal>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
