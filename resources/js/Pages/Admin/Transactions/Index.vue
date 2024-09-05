<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { MinusCircle, PlusCircle } from "lucide-vue-next";
	import { HiSolidExclamation, HiTrash } from "oh-vue-icons/icons";

	import AdminTableLink from "@/Components/AdminTableLink.vue";
	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		transactions: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteTransactionForm = useForm({});
	const reverseTransactionForm = useForm({});
	const transactionBeingDeleted = ref(null);
	const transactionBeingReversed = ref(null);

	const deleteTransaction = () => {
		deleteTransactionForm.delete(
			window.route("admin.transactions.destroy", {
				transaction: transactionBeingDeleted.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (transactionBeingDeleted.value = null),
			},
		);
	};
	const reverseTransaction = () => {
		reverseTransactionForm.put(
			window.route("admin.transactions.reverse", {
				transaction: transactionBeingReversed.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (transactionBeingReversed.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.transactions.index"),
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
	<Head :title="title ?? 'Transactions'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Transactions Log") }}
							</h3>
							<p>
								{{
									$t(
										"This is a log of all executed transactions on the system",
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
													{{ $t("Description") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Amount") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Balance Before") }}
												</th>

												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Type") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="transaction in transactions.data"
												:key="transaction.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div>
															<a
																@click="
																	search =
																		transaction
																			.user
																			.email
																"
																class="underline"
																href="#">
																{{
																	transaction.uid
																}}
															</a>
														</div>
														<a
															target="_blank"
															class="text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300"
															:href="
																route(
																	'admin.users.show',
																	transaction
																		.user
																		.id,
																)
															">
															{{
																transaction.user
																	.email
															}}
														</a>
													</div>
												</td>
												<td
													class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-300">
													{{
														transaction.description
													}}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div
														class="flex items-center space-x-2">
														<PlusCircle
															class="w-4 h-4 text-green-600 dark:text-green-400"
															v-if="
																transaction.action ===
																'credit'
															" />
														<MinusCircle
															class="w-4 h-4 text-red-600 dark:text-red-400"
															v-else />
														<span>
															{{
																transaction.amount
															}}
														</span>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													{{
														transaction.balance_before
													}}
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													{{ transaction.type }}
												</td>
												<td role="cell">
													<div
														class="flex justify-end space-x-3 text-lg">
														<AdminTableLink
															href="#"
															class="hover:text-yellow-500"
															v-tippy="
																$t('Reverse')
															"
															@click.prevent="
																transactionBeingReversed =
																	transaction
															">
															<VueIcon
																:icon="
																	HiSolidExclamation
																"
																class="w-4 h-4" />
														</AdminTableLink>
														<AdminTableLink
															href="#"
															class="hover:text-red-500"
															@click.prevent="
																transactionBeingDeleted =
																	transaction
															">
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4" />
														</AdminTableLink>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="transactions.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="transactionBeingDeleted"
			@close="transactionBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting #{transaction} ?", {
						transaction: transactionBeingDeleted.uid,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will only remove the transaction from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{
						$t(
							"Its Recommended to attempt to reverse transaction Instead",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="transactionBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteTransaction"
					:class="{ 'opacity-25': deleteTransactionForm.processing }"
					:disabled="deleteTransactionForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="transactionBeingReversed"
			@close="transactionBeingReversed = null">
			<template #title>
				{{
					$t("Are you sure about reversing  #{transaction} ?", {
						transaction: transactionBeingReversed.uid,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"Reversing the transaction will only affect the users balance and will not undo all side effects eg withdrawn balance!",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					secondary
					class="uppercase text-xs font-semibold"
					@click="transactionBeingReversed = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					primary
					class="ml-2 uppercase text-xs font-semibold"
					@click="reverseTransaction"
					:class="{ 'opacity-25': reverseTransactionForm.processing }"
					:disabled="reverseTransactionForm.processing">
					{{ $t("Reverse") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
