<script setup>
	import { computed, ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiSolidPlus } from "oh-vue-icons/icons";
	import { uid } from "uid";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { ucfirst } from "@/hooks";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	const props = defineProps({
		commissions: Object,
		title: { required: false, type: String },
		type: String,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteCommissionForm = useForm({});
	const commissionBeingDeleted = ref(null);

	const deleteCommission = () => {
		deleteCommissionForm.delete(
			window.route(
				"admin.commissions.destroy",
				commissionBeingDeleted.value?.id,
			),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (commissionBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.commissions.index"),
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

	const toggle = (commission) => {
		commission.busy = true;
		router.put(
			window.route("admin.commissions.toggle", commission.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					commission.busy = false;
					commissionBeingDeleted.value = null;
				},
			},
		);
	};
	const options = [
		{ key: uid(), value: "deposit", label: "Deposit" },
		{ key: uid(), value: "slip", label: "Slip Win" },
		{ key: uid(), value: "ticket", label: "Ticket Win" },
		{ key: uid(), value: "cancellation", label: "Cancellation" },
	];
	const type = computed({
		get: () => props.type,
		set: (val) =>
			router.visit(
				window.route("admin.commissions.index", { type: val }),
			),
	});
	const generate = ref(5);
	const create = () => {
		if (parseInt(generate.value < 1)) return;
		router.visit(
			window.route("admin.commissions.create", {
				type: props.type,
				num: generate.value,
			}),
		);
	};
</script>
<template>
	<Head :title="title ?? 'Commissions'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ type ? ucfirst(type) : "Deposit" }}
								{{ $t("Commissions") }}
							</h3>
							<p
								class="text-gray-700 text-base dark:text-gray-200">
								{{
									$t(
										"Commission is calculated as a percentage of admins collected fees",
									)
								}}
							</p>
							<p class="text-sm" v-if="type == 'cancellation'">
								{{
									$t(
										"Commission is paid to upline when bet is cancelled",
									)
								}}
							</p>
							<p class="text-sm" v-else-if="type == 'deposit'">
								{{
									$t(
										"Commission is paid to upline when user deposits",
									)
								}}
							</p>
							<p class="text-sm" v-else-if="type == 'slip'">
								{{
									$t(
										"Commission is paid to upline when exchange slip wins.",
									)
								}}
							</p>
							<p class="text-sm" v-else>
								{{
									$t(
										"Commission is paid to upline when bookie ticket wins",
									)
								}}
							</p>
						</div>
						<RadioSelect
							class="max-w-2xl"
							v-model="type"
							:options="options" />
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center gap-4 justify-end mb-4 px-6">
								<FormInput
									v-model="generate"
									placeholder="Number of levels">
									<template #trail>Levels</template>
								</FormInput>
								<PrimaryButton
									:disabled="(parseInt(generate) ?? 0) < 1"
									class="!py-1.5 uppercase text-xs font-semibold"
									primary=""
									@click="create">
									<Loading
										v-if="loading"
										class="w-4 h-4 -ml-2 mr-2 inline-block" />
									<VueIcon
										class="-ml-2 mr-1"
										:icon="HiSolidPlus" />
									{{ $t("Rebuild") }}
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
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Level") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Percent") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="commission in commissions"
												:key="commission.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
													LEVEL #
													{{ commission.level }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
													{{ commission.percent * 1 }}
													%
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600 dark:text-gray-300">
													<div
														class="flex w-full justify-end">
														<Switch
															:model-value="
																commission.active
															"
															@update:model-value="
																toggle
															">
															<span
																v-if="
																	commission.active
																">
																Enabled
															</span>
															<span v-else>
																Disabled
															</span>
														</Switch>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination
									v-if="commissions.meta"
									:meta="commissions.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="commissionBeingDeleted"
			@close="commissionBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {commission} ?", {
						commission: commissionBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the commission from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{
						$t("Its Recommended to Disable the commission Instead")
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="commissionBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="commissionBeingDeleted.active"
					@click="toggle(commissionBeingDeleted)">
					<Loading v-if="commissionBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteCommission"
					:class="{ 'opacity-25': deleteCommissionForm.processing }"
					:disabled="deleteCommissionForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
