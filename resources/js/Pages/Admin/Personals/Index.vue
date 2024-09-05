<script setup>
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import Loading from "@/Components/Loading.vue";
import Pagination from "@/Components/Pagination.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SearchInput from "@/Components/SearchInput.vue";
import VueIcon from "@/Components/VueIcon.vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
import { HiPencil, HiTrash } from "oh-vue-icons/icons";
import { ref } from "vue";
	defineProps({
		personals: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deletePersonalForm = useForm({});
	const personalBeingDeleted = ref(null);

	const deletePersonal = () => {
		deletePersonalForm.delete(
			window.route("admin.personals.destroy", personalBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (personalBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.personals.index"),
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

	const toggle = (personal) => {
		personal.busy = true;
		router.put(
			window.route("admin.personals.toggle", personal.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					personal.busy = false;
					personalBeingDeleted.value = null;
				},
			},
		);
	};
</script>
<template>
	<Head :title="title ?? 'Personals'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Accepted Personals") }}
							</h3>
							<p>{{ $t("Available Personals") }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								@click="updatePersonals"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800">
								<Loading
									v-if="loading"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Check for New Personals") }}
							</button>
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
												<th role="columnheader">
													{{ $t("Personal") }}
												</th>
																					<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('User Id')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Proof Of Identity')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Proof Of Identity Type')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Proof Of Address')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Proof Of Address Type')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Bet Emails')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Mailing List')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Confirm Bets')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Daily Gross Deposit')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Weekly Gross Deposit')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Monthly Gross Deposit')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Loss Limit Interval')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Loss Limit')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Stake Limit')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Time Out At')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Dob')}}</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="personal in personals.data"
												:key="personal.id"
												role="row">
																					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.user_id }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.proof_of_identity }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.proof_of_identity_type }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.proof_of_address }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.proof_of_address_type }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.bet_emails }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.mailing_list }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.confirm_bets }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.daily_gross_deposit }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.weekly_gross_deposit }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.monthly_gross_deposit }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.loss_limit_interval }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.loss_limit }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.stake_limit }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.time_out_at }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ personal.dob }}</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<Link
															:href="
																route(
																	'admin.personals.edit',
																	personal.id,
																)
															"
															class="cursor-pointer p-2 hover:text-blue-600">
															<VueIcon
																:icon="HiPencil"
																class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																personalBeingDeleted =
																	personal
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
								<Pagination :meta="personals.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="personalBeingDeleted"
			@close="personalBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {personal} ?", {
						personal: personalBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the personal from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{
						$t(
							"Its Recommended to Disable the personal Instead",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="personalBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="personalBeingDeleted.active"
					@click="toggle(personalBeingDeleted)">
					<Loading v-if="personalBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deletePersonal"
					:class="{ 'opacity-25': deletePersonalForm.processing }"
					:disabled="deletePersonalForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
