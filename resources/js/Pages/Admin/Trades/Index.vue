<script setup>
	import { Head } from "@inertiajs/vue3";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	import Pagination from "@/Components/Pagination.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		trades: Object,
		title: { required: false, type: String },
	});
</script>
<template>
	<Head :title="title ?? 'Trades'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Trades Executed on Website") }}
							</h3>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
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
													{{ $t("Info") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Amount") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Buy") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Sell") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Margin") }} /{{
														$t("Commission")
													}}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="trade in trades.data"
												:key="trade.id"
												role="row">
												<td
													class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div>
															{{
																trade.maker
																	.game_info
															}}
														</div>
														<div
															class="uppercase text-emerald-500 text-xs font-bold font-inter">
															{{
																trade.maker
																	.market_info
															}}
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<MoneyFormat
														:amount="
															trade.amount
														" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<OddsFormat
														:odds="trade.buy" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<OddsFormat
														:odds="trade.sell" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<MoneyFormat
														:amount="
															trade.margin
														" />
													<span
														class="text-emerald-500">
														/
													</span>
													<MoneyFormat
														:amount="
															trade.commission ??
															0
														" />
												</td>
												<td role="cell">
													<StatusBadge
														:status="
															trade.status
														" />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="trades.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
