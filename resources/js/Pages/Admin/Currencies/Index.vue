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
		currencies: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteCurrencyForm = useForm({});
	const currencyBeingDeleted = ref(null);

	const deleteCurrency = () => {
		deleteCurrencyForm.delete(
			window.route("admin.currencies.destroy", currencyBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (currencyBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.currencies.index"),
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

	const toggle = (currency) => {
		currency.busy = true;
		router.put(
			window.route("admin.currencies.toggle", currency.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					currency.busy = false;
					currencyBeingDeleted.value = null;
				},
			},
		);
	};
</script>
<template>
	<Head :title="title ?? 'Currencys'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Accepted Currencys") }}
							</h3>
							<p>{{ $t("Available Currencys") }}</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<button
								type="button"
								@click="updateCurrencys"
								class="focus:outline-none text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-emerald-600 dark:hover:bg-emerald-700 dark:focus:ring-emerald-800">
								<Loading
									v-if="loading"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Check for New Currencys") }}
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
													{{ $t("Currency") }}
												</th>
																					<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Gateway')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Code')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Name')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Regex')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Logo Url')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Chain')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Contract')}}</th>
									<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{$t('Explorer')}}</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="currency in currencies.data"
												:key="currency.id"
												role="row">
																					<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.gateway }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.code }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.name }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.regex }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.logo_url }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.chain }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.contract }}</td>
									<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" >{{ currency.explorer }}</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<Link
															:href="
																route(
																	'admin.currencies.edit',
																	currency.id,
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
																currencyBeingDeleted =
																	currency
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
								<Pagination :meta="currencies.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="currencyBeingDeleted"
			@close="currencyBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting {currency} ?", {
						currency: currencyBeingDeleted.name,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the currency from the database and cannot be undone",
						)
					}}
				</p>
				<p>
					{{
						$t(
							"Its Recommended to Disable the currency Instead",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="currencyBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="currencyBeingDeleted.active"
					@click="toggle(currencyBeingDeleted)">
					<Loading v-if="currencyBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteCurrency"
					:class="{ 'opacity-25': deleteCurrencyForm.processing }"
					:disabled="deleteCurrencyForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
