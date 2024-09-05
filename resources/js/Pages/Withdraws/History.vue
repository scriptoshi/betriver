<script setup>
	import { Link } from "@inertiajs/vue3";
	import { Search } from "lucide-vue-next";

	import FormInput from "@/Components/FormInput.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Pagination from "@/Components/Pagination.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	defineProps({
		withdraws: Object,
	});
</script>

<template>
	<div class="my-6 pt-8">
		<div class="flex flex-col sm:flex-row mb-1 gap-4 sm:justify-between">
			<h3>History</h3>
			<FormInput
				:placeholder="$t('Search transactions')"
				class="max-w-xs w-full">
				<template #lead>
					<Search class="w-4 h-4 text-gray-400" />
				</template>
			</FormInput>
		</div>
		<div class="overflow-x-auto">
			<table
				class="table-default table-hover border-separate border-spacing-x-0 border-spacing-y-3"
				role="table">
				<thead class="hidden">
					<tr role="row">
						<th role="columnheader">
							{{ $t("WID") }}
						</th>

						<th role="columnheader">
							{{ $t("Gateway") }}
						</th>

						<th
							scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $t("Amount") }}
						</th>
						<th
							scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $t("Status") }}
						</th>
					</tr>
				</thead>
				<tbody role="rowgroup">
					<tr
						v-for="withdraw in withdraws.data"
						:key="withdraw.id"
						role="row">
						<td
							class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div class="grid">
								<Link
									:href="
										route('withdraws.show', {
											withdraw: withdraw.uuid,
										})
									"
									class="text-sky-600 mb-1 dark:text-sky-400 uppercase underline">
									<span>#{{ withdraw.uid }}</span>
								</Link>
								<span
									class="text-[10px] font-semibold text-gray-400">
									{{ withdraw.date }}
								</span>
							</div>
						</td>
						<td
							class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div>
								<div class="flex items-center">
									<img
										class="w-7 h-7 rounded-full mr-3"
										:src="withdraw.gateway.logo" />
									<div>
										<div class="">
											{{ withdraw.gateway.name }}
										</div>
										<div
											class="text-[10px] uppercase font-semibold text-gray-400">
											---
										</div>
									</div>
								</div>
							</div>
						</td>
						<td
							class="px-6 py-4 uppercase whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div>
								<div>
									<MoneyFormat
										class="text-lg text-gray-700 dark:text-gray-150"
										:amount="withdraw.amount" />
								</div>
								<span
									class="text-[10px] font-semibold text-gray-400 dark:text-gray-400">
									{{
										withdraw.amount.gateway_amount ?? "---"
									}}
									{{ withdraw.gateway_currency }}
								</span>
							</div>
						</td>
						<td
							class="px-6 py-4 text-right whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<StatusBadge :status="withdraw.status" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<Pagination :meta="withdraws.meta" />
	</div>
</template>
