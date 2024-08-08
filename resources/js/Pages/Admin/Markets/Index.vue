<script setup>
	import { ref } from "vue";

	import { Head, router } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";

	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		markets: Object,
		sport: String,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const marketBeingDeleted = ref(null);

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
						</div>
					</div>
					<div
						class="card bg-gray-60 dark:bg-gray-900 border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-end mb-4 px-6">
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
										<tbody role="rowgroup">
											<tr
												v-for="market in markets"
												:key="market.id"
												role="row">
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
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
