<script setup>
	import { computed, ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { CheckIcon } from "lucide-vue-next";
	import {
		HiSolidCheck,
		HiSolidChevronDown,
		HiSolidX,
		PrQrcode,
		RiRefreshLine,
	} from "oh-vue-icons/icons";

	import AdminTableLink from "@/Components/AdminTableLink.vue";
	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import FormInput from "@/Components/FormInput.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import StatusBadge from "@/Components/StatusBadge.vue";
	import { Badge } from "@/Components/ui/badge";
	import {
		Popover,
		PopoverContent,
		PopoverTrigger,
	} from "@/Components/ui/popover";
	import VueIcon from "@/Components/VueIcon.vue";
	import WeCopy from "@/Components/WeCopy.vue";
	import { shortenAddress } from "@/hooks/address";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	const props = defineProps({
		withdraws: Object,
		nowpayments: Array,
		statuses: Object,
		title: { required: false, type: String },
	});

	const processForm = useForm({
		ids: [],
		username: null,
		password: null,
	});
	const hasNowPayments = computed(() =>
		props.nowpayments?.some((r) => processForm.ids.includes(r)),
	);
	const confirmProcess = ref(false);
	const processWithdraw = () => {
		processForm.put(window.route("admin.withdraws.batch"), {
			preserveScroll: true,
			preserveState: true,
			onSuccess: () => processForm.reset(),
		});
	};
	// 2fa
	const withdraw2FaForm = useForm({
		code: null,
	});
	const withdraw2Fa = ref();
	const submit2Fa = () => {
		withdraw2FaForm.post(
			window.route("admin.withdraws.twofa", {
				withdraw: withdraw2Fa.value.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => withdraw2FaForm.reset(),
			},
		);
	};

	// refresh
	const refreshForm = useForm({ id: null });
	const refresh = (withdraw) => {
		refreshForm.id = withdraw.id;
		processForm.put(
			window.route("admin.withdraws.refresh", {
				withdraw: withdraw.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => refreshForm.reset(),
			},
		);
	};
	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const failWithdrawForm = useForm({});
	const completeWithdrawForm = useForm({});
	const withdrawBeingFailed = ref(null);
	const withdrawBeingCompleted = ref(null);

	const failWithdraw = () => {
		failWithdrawForm.delete(
			window.route("admin.withdraws.fail", {
				withdraw: withdrawBeingFailed.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (withdrawBeingFailed.value = null),
			},
		);
	};
	const statusForm = useForm({ status: null });
	const changeStatus = (status, wuuid) => {
		console.log(status);
		statusForm.status = status;
		statusForm.put(
			window.route("admin.withdraws.status", {
				withdraw: wuuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => statusForm.reset(),
			},
		);
	};
	const completeWithdraw = () => {
		completeWithdrawForm.put(
			window.route("admin.withdraws.complete", {
				withdraw: withdrawBeingCompleted.value?.uuid,
			}),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (withdrawBeingCompleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search, () => params.status, () => params.items],
		([search, status, items]) => {
			router.get(
				window.route("admin.withdraws.index"),
				{
					...(search ? { search } : {}),
					...(status ? { status } : {}),
					...(items ? { items } : {}),
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
	<Head :title="title ?? 'Withdraws'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Withdraws Log") }}
							</h3>
							<p>
								{{
									$t(
										"This is a log of all executed withdraws on the system",
									)
								}}
							</p>
							<p>
								{{
									$t(
										"To refund user, click on status and change  to reversed",
									)
								}}
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3"></div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div class="lg:flex items-center gap-4 mb-4 px-6">
								<SearchInput
									class="max-w-xs w-full"
									v-model="search" />
								<div class="max-w-[200px] w-full">
									<Multiselect
										:options="statuses"
										valueProp="value"
										label="label"
										class="md"
										:placeholder="$t('Filter Status')"
										v-model="params.status"
										searchable
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
								<PrimaryButton
									v-if="processForm.ids?.length > 0"
									@click.prevent="confirmProcess = true"
									class="ml-auto"
									primary>
									Process {{ processForm.ids?.length }}
								</PrimaryButton>
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
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
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
												v-for="withdraw in withdraws.data"
												:key="withdraw.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<label
														v-if="
															withdraw.status ===
															'review'
														"
														class="inline-flex items-center space-x-2">
														<input
															v-model="
																processForm.ids
															"
															:value="withdraw.id"
															class="form-checkbox basic size-5 rounded border-gray-400/70 checked:bg-gray-500 checked:border-gray-500 hover:border-gray-500 focus:border-gray-500 dark:border-navy-400 dark:checked:bg-navy-400"
															type="checkbox" />
													</label>
													<label
														v-else
														class="inline-flex opacity-50 items-center space-x-2">
														<input
															checked
															disabled
															class="form-checkbox size-5 rounded border-gray-400/70 checked:bg-gray-500 checked:border-gray-500 hover:border-gray-500 focus:border-gray-500 dark:border-navy-400 dark:checked:bg-navy-400"
															type="checkbox" />
													</label>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<div>
															<a
																@click="
																	search =
																		withdraw
																			.user
																			.email
																"
																class="underline"
																href="#">
																{{
																	withdraw.uid
																}}
															</a>
														</div>
														<a
															target="_blank"
															class="text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300"
															:href="
																route(
																	'admin.users.show',
																	withdraw
																		.user
																		.id,
																)
															">
															{{
																shortenAddress(
																	withdraw
																		.user
																		.email,
																	16,
																	6,
																)
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
																withdraw.gateway
																	.logo
															" />
														<div>
															<span>
																{{
																	withdraw
																		.gateway
																		.name
																}}
															</span>

															<div
																class="text-xs text-gray-400">
																<WeCopy
																	after
																	v-if="
																		withdraw.to
																	"
																	:text="
																		withdraw.to
																	">
																	{{
																		shortenAddress(
																			withdraw.to,
																			14,
																			6,
																		)
																	}}
																</WeCopy>
															</div>
														</div>
													</div>
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<div>
														<MoneyFormat
															:amount="
																withdraw.amount
															" />
														<div
															class="text-xs text-gray-400">
															fees:
															<MoneyFormat
																:amount="
																	withdraw.fees
																" />
														</div>
													</div>
												</td>

												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
													<Popover>
														<PopoverTrigger>
															<StatusBadge
																:status="
																	withdraw.status
																" />
														</PopoverTrigger>
														<PopoverContent
															class="max-w-[160px] bg-gray-250 dark:bg-gray-850">
															<div
																class="grid gap-2 w-full">
																<a
																	v-for="status in statuses"
																	:key="
																		status.value
																	"
																	class="w-full group"
																	@click.prevent="
																		changeStatus(
																			status.value,
																			withdraw.uuid,
																		)
																	"
																	href="#">
																	<Badge
																		variant="outline"
																		class="uppercase group-hover:bg-gray-150 dark:group-hover:bg-gray-700 w-full text-center font-semibold font-inter"
																		:class="{
																			'border-gray-400 dark:border-gray-650 text-gray-600 dark:text-gray-400':
																				[
																					'review',
																					'pending',
																				].includes(
																					status.value,
																				),
																			'border-sky-400 dark:border-sky-650 text-sky-600 dark:text-sky-400':
																				[
																					'processing',
																				].includes(
																					status.value,
																				),
																			'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
																				[
																					'approved',
																					'complete',
																					'confirmed',
																				].includes(
																					status.value,
																				),
																			'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
																				[
																					'rejected',
																					'failed',
																					'cancelled',
																					'reversed',
																				].includes(
																					status.value,
																				),
																		}">
																		<CheckIcon
																			class="w-4 h-4"
																			v-if="
																				status.value ==
																				withdraw.status
																			" />
																		<span
																			class="w-full text-center">
																			{{
																				status.value
																			}}
																		</span>
																	</Badge>
																</a>
															</div>
														</PopoverContent>
													</Popover>
												</td>
												<td role="cell">
													<div
														class="flex justify-end space-x-3 text-lg">
														<AdminTableLink
															href="#"
															class="hover:text-sky-500"
															v-if="
																withdraw.status ==
																	'processing' &&
																withdraw.gateway
																	.gid ===
																	'nowpayments'
															"
															v-tippy="
																$t(
																	'Provide 2FA code',
																)
															"
															@click.prevent="
																withdraw2Fa =
																	withdraw
															">
															<VueIcon
																:icon="PrQrcode"
																class="w-4 h-4" />
														</AdminTableLink>
														<AdminTableLink
															href=""
															class="hover:text-sky-500"
															:disabled="
																refreshForm.processing &&
																refreshForm.id ===
																	withdraw.id
															"
															v-if="
																withdraw.status ==
																'processing'
															"
															@click.stop="
																refresh(
																	withdraw,
																)
															"
															v-tippy="
																$t('Refresh')
															"
															button>
															<VueIcon
																:icon="
																	RiRefreshLine
																"
																:class="{
																	'animate-spin':
																		refreshForm.processing &&
																		refreshForm.id ===
																			withdraw.id,
																}"
																class="w-4 h-4" />
														</AdminTableLink>
														<AdminTableLink
															href="#"
															class="hover:text-green-500"
															v-tippy="
																$t(
																	'Force Complete',
																)
															"
															@click.prevent="
																withdrawBeingCompleted =
																	withdraw
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
																withdrawBeingFailed =
																	withdraw
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
								<Pagination
									v-model:perPage="params.items"
									showPerPage
									:meta="withdraws.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal :show="withdraw2Fa" @close="withdraw2Fa = null">
			<template #title>
				{{
					$t("Provide Nowpayments 2fa code", {
						withdraw: withdraw2Fa.uid,
					})
				}}
			</template>

			<template #content>
				<div class="p-4 rounded bg-gray-100 dark:bg-gray-850">
					<p>
						{{
							$t(
								"If your account at Nowpayments doesnt have 2FA enabled, You should check your nowpayments email for the 2fa code",
							)
						}}
					</p>
					<p>
						{{
							$t(
								"All withdraws in the same batch will be approved!",
							)
						}}
					</p>
				</div>
				<FormInput
					class="max-w-xs my-4 w-full"
					inputClasses="!text-lg !tracking-[0.4em] font-mono"
					label="Two factor auth code"
					v-model="withdraw2FaForm.code" />
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="withdraw2Fa = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="submit2Fa"
					:class="{ 'opacity-25': withdraw2FaForm.processing }"
					:disabled="
						withdraw2FaForm.processing ||
						!withdraw2FaForm.code ||
						withdraw2FaForm.code?.length < 4
					">
					{{ $t("Submit 2FA Code") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="withdrawBeingFailed"
			@close="withdrawBeingFailed = null">
			<template #title>
				{{
					$t("Are you sure about force failing #{withdraw} ?", {
						withdraw: withdrawBeingFailed.uid,
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
							"Its Recommended to find and attempt to reverse withdraw transaction instead under transactions",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="withdrawBeingFailed = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="failWithdraw"
					:class="{ 'opacity-25': failWithdrawForm.processing }"
					:disabled="failWithdrawForm.processing">
					{{ $t("Force fail") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="withdrawBeingCompleted"
			@close="withdrawBeingCompleted = null">
			<template #title>
				{{
					$t("Are you sure about force completing  #{withdraw} ?", {
						withdraw: withdrawBeingCompleted.uid,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This action will mark the withdraw as complete and execute a balance update transaction, regardless of current state of the withdraw",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					secondary
					class="uppercase text-xs font-semibold"
					@click="withdrawBeingCompleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					class="ml-2 uppercase text-xs font-semibold"
					@click="completeWithdraw"
					:class="{ 'opacity-25': completeWithdrawForm.processing }"
					:disabled="completeWithdrawForm.processing">
					{{ $t("Force complete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="withdrawBeingFailed"
			@close="withdrawBeingFailed = null">
			<template #title>
				{{
					$t("Are you sure about force failing #{withdraw} ?", {
						withdraw: withdrawBeingFailed.uid,
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
							"Its Recommended to find and attempt to reverse withdraw transaction instead under transactions",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="withdrawBeingFailed = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="failWithdraw"
					:class="{ 'opacity-25': failWithdrawForm.processing }"
					:disabled="failWithdrawForm.processing">
					{{ $t("Force fail") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>

		<ConfirmationModal
			:show="confirmProcess"
			@close="confirmProcess = false">
			<template #title>
				{{ $t("Confirm gateway processing") }}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This action will initiate a mass payout at the respective payment gateways.",
						)
					}}
				</p>
				<p class="mb-4">
					{{
						$t(
							"Please note: for most gateways you will still need to to approve the process via email from the gateway or 2FA at the gateway!",
						)
					}}
				</p>
				<div
					v-if="hasNowPayments"
					class="p-4 mt-5rounded bg-gray-100 dark:bg-gray-850">
					<div class="grid sm:grid-cols-2 gap-4">
						<FormInput
							v-model="processForm.username"
							autocomplete="one-time-code"
							label="Nowpayments username" />
						<FormInput
							type="password"
							autocomplete="one-time-code"
							v-model="processForm.password"
							label="Nowpayments password" />
					</div>
				</div>
			</template>

			<template #footer>
				<PrimaryButton
					secondary
					class="uppercase text-xs font-semibold"
					@click="confirmProcess = false">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					class="ml-2 uppercase text-xs font-semibold"
					@click="processWithdraw"
					:class="{ 'opacity-25': processForm.processing }"
					:disabled="processForm.processing">
					{{ $t("Process withdrawals") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
