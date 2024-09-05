<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiTrash } from "oh-vue-icons/icons";

	import DialogModal from "@/Components/DialogModal.vue";
	import Flag from "@/Components/Flag";
	import FormLabel from "@/Components/FormLabel.vue";
	import FormTextArea from "@/Components/FormTextArea.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import { Badge } from "@/Components/ui/badge";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		whitelists: Object,
		pending: Number,
		title: { required: false, type: String },
	});
	const open = ref(false);
	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const massApproveForm = useForm({
		list: "",
	});
	const massApprove = () => {
		massApproveForm.post(window.route("admin.whitelists.mass.approve"), {
			preserveScroll: true,
			preserveState: true,
			onSuccess() {
				open.value = false;
			},
		});
	};
	const massReject = () => {
		massApproveForm.post(window.route("admin.whitelists.mass.reject"), {
			preserveScroll: true,
			preserveState: true,
			onSuccess() {
				open.value = false;
			},
		});
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.whitelists.index"),
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
	<Head :title="title ?? 'Whitelists'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("User whitelisted  payout addresses") }}
							</h3>
							<p>Pending your review : {{ pending }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton secondary @click="open = true">
								{{ $t("Mass approve") }}
							</PrimaryButton>
							<PrimaryButton
								@click="updateWhitelists"
								:disabled="pending == 0"
								url
								:href="route('admin.whitelists.download')">
								{{ $t("Download pending") }}
								<span class="ml-2" v-if="pending > 0">
									({{ pending }})
								</span>
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-between mb-4 px-6">
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
													{{ $t("Payout Address") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Status") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="whitelist in whitelists.data"
												:key="whitelist.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
													<div
														class="flex items-center">
														<img
															class="w-7 h-7 rounded-full mr-3"
															:src="
																whitelist
																	.currency
																	.gateway
																	.logo
															" />
														<div>
															<div class="">
																{{
																	whitelist
																		.currency
																		.gateway
																		.name
																}}
															</div>
															<div
																class="text-[10px] font-semibold text-gray-400">
																#{{
																	whitelist
																		.user
																		.email
																}}
															</div>
														</div>
													</div>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
													<div
														class="flex items-center">
														<Flag
															class="w-7 h-7 rounded-full mr-3"
															:iso="
																whitelist
																	.currency
																	.logo_url
															"
															v-if="
																whitelist
																	.currency
																	.logo_url
																	.length == 3
															" />
														<img
															v-else
															class="w-7 h-7 rounded-full mr-3"
															:src="
																whitelist
																	.currency
																	.logo_url
															" />
														<div>
															<div class="">
																{{
																	whitelist
																		.currency
																		.name
																}}
															</div>
															<div
																class="text-[10px] font-semibold text-gray-400">
																<WeCopy
																	after
																	:text="
																		whitelist.payout_address
																	">
																	{{
																		whitelist.payout_address
																	}}
																</WeCopy>
															</div>
														</div>
													</div>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700 dark:text-gray-300">
													<Badge
														variant="outline"
														class="uppercase font-semibold font-inter"
														:class="{
															'border-gray-400 dark:border-gray-650 text-gray-600 dark:text-gray-400':
																[
																	'review',
																	'pending',
																	'processing',
																].includes(
																	whitelist.status,
																),
															'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
																[
																	'approved',
																	'complete',
																].includes(
																	whitelist.status,
																),
															'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
																[
																	'rejected',
																	'failed',
																].includes(
																	whitelist.status,
																),
														}">
														{{ whitelist.status }}
													</Badge>
												</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<a
															href="#"
															@click.prevent="
																whitelistBeingDeleted =
																	whitelist
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
								<Pagination :meta="whitelists.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<DialogModal
			:show="open"
			@close="open = false"
			:closeable="true"
			maxWidth="xl">
			<template #title>
				<h3 class="text-lg">Mass approve addresses</h3>
				<p class="text-sm">
					Enter addresses separated by comma, space or new line
				</p>
			</template>
			<template #content>
				<div>
					<FormLabel class="mb-2">Addresses to approve</FormLabel>
					<FormTextArea v-model="massApproveForm.list" :rows="4" />
					<p
						class="text-red-500 dark:text-red-400"
						v-if="massApproveForm.errors.list">
						{{ massApproveForm.errors.list }}
					</p>
				</div>
				<div class="flex mt-6 space-x-2 justify-end">
					<PrimaryButton @click.prevent="open = false" secondary>
						Cancel
					</PrimaryButton>
					<PrimaryButton
						:disabled="massApproveForm.processing"
						@click.prevent="massReject"
						error>
						Reject all
					</PrimaryButton>
					<PrimaryButton
						:disabled="massApproveForm.processing"
						@click.prevent="massApprove"
						primary>
						Approve all
					</PrimaryButton>
				</div>
			</template>
		</DialogModal>
	</AdminLayout>
</template>
