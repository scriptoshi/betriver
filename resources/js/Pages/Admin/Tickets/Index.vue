<script setup>
	import { ref } from "vue";

	import { Head, router } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";

	import Pagination from "@/Components/Pagination.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import { ucfirst } from "@/hooks";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import TicketRow from "@/Pages/Admin/Tickets/TicketRow.vue";

	defineProps({
		tickets: Object,
		filter: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");

	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.tickets.index"),
				{ search },
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
	<Head :title="'Tickets'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ filter ? ucfirst(filter) : "All" }}
								{{ $t("Tickets") }}
							</h3>
							<p>{{ $t("Available Tickets") }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3"></div>
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
													{{ $t("User Id") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Uid") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Amount") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Payout") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Odds") }}
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

												<td
													class="px-6 py-3 text-center"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<TicketRow
												v-for="ticket in tickets.data"
												:key="ticket.id"
												:ticket="ticket" />
										</tbody>
									</table>
								</div>
								<Pagination :meta="tickets.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
