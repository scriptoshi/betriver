<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiSolidCheck, HiSolidX } from "oh-vue-icons/icons";

	import AdminTableLink from "@/Components/AdminTableLink.vue";
	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import { Badge } from "@/Components/ui/badge";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		deposits: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const failDepositForm = useForm({});
	const completeDepositForm = useForm({});
	const depositBeingFailed = ref(null);
	const depositBeingCompleted = ref(null);

	const failDeposit = () => {
		failDepositForm.delete(
			window.route("admin.deposits.fail", {
				deposit: depositBeingFailed.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (depositBeingFailed.value = null),
			},
		);
	};
	const completeDeposit = () => {
		completeDepositForm.put(
			window.route("admin.deposits.complete", {
				deposit: depositBeingCompleted.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (depositBeingCompleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.deposits.index"),
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
	<Head :title="title ?? 'Deposits'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Deposits Log") }}
							</h3>
							<p>
								{{
									$t(
										"This is a log of all executed deposits on the system",
									)
								}}
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3"></div>
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
													{{ $t("UID") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="deposit in deposits.data"
												:key="deposit.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div>
															<a
																@click="
																	search =
																		deposit
																			.user
																			.email
																"
																class="underline"
																href="#">
																{{
																	deposit.uid
																}}
															</a>
														</div>
														<a
															target="_blank"
															class="text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300"
															:href="
																route(
																	'admin.users.show',
																	deposit.user
																		.id,
																)
															">
															{{
																deposit.user
																	.email
															}}
														</a>
													</div>
												</td>
												<td
													class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-3">
														<img
															class="w-6 h-6 rounded-full"
															:src="
																deposit.gateway
																	.logo
															" />
														<div>
															<span>
																{{
																	deposit
																		.gateway
																		.name
																}}
															</span>
															<div
																class="text-xs text-gray-400">
																{{
																	deposit.remoteId
																}}
															</div>
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<MoneyFormat
															:amount="
																deposit.amount
															" />
														<div
															class="text-xs text-gray-400">
															fees:
															<MoneyFormat
																:amount="
																	deposit.fees
																" />
														</div>
													</div>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
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
																	deposit.status,
																),
															'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
																[
																	'approved',
																	'complete',
																].includes(
																	deposit.status,
																),
															'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
																[
																	'rejected',
																	'failed',
																	'reversed',
																].includes(
																	deposit.status,
																),
														}">
														{{ deposit.status }}
													</Badge>
												</td>
												<td role="cell">
													<div
														class="flex justify-end space-x-3 text-lg">
														<AdminTableLink
															href="#"
															class="hover:text-green-500"
															v-tippy="
																$t(
																	'Force Complete',
																)
															"
															@click.prevent="
																depositBeingCompleted =
																	deposit
															">
															<VueIcon
																:icon="
																	HiSolidCheck
																"
																class="w-4 h-4" />
														</AdminTableLink>
														<AdminTableLink
															href="#"
															v-tippy="
																$t('Force Fail')
															"
															class="hover:text-red-500"
															@click.prevent="
																depositBeingFailed =
																	deposit
															">
															<VueIcon
																:icon="HiSolidX"
																class="w-4 h-4" />
														</AdminTableLink>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="deposits.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="depositBeingFailed"
			@close="depositBeingFailed = null">
			<template #title>
				{{
					$t("Are you sure about force failing #{deposit} ?", {
						deposit: depositBeingFailed.uid,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will override any status to failed. It will not undo any side effects eg Balance update",
						)
					}}
				</p>
				<p>
					{{
						$t(
							"Its Recommended to find and attempt to reverse deposit transaction instead under transactions",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="depositBeingFailed = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="failDeposit"
					:class="{ 'opacity-25': failDepositForm.processing }"
					:disabled="failDepositForm.processing">
					{{ $t("Force fail") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="depositBeingCompleted"
			@close="depositBeingCompleted = null">
			<template #title>
				{{
					$t("Are you sure about force completing  #{deposit} ?", {
						deposit: depositBeingCompleted.uid,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This action will mark the deposit as complete and execute a balance update transaction, regardless of current state of the deposit",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					secondary
					class="uppercase text-xs font-semibold"
					@click="depositBeingCompleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					class="ml-2 uppercase text-xs font-semibold"
					@click="completeDeposit"
					:class="{ 'opacity-25': completeDepositForm.processing }"
					:disabled="completeDepositForm.processing">
					{{ $t("Force complete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
