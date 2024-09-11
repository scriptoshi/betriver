<script setup>
	import { Link, router } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiSearch, HiSolidX } from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import DateRangeFilter from "@/Pages/Account/Statement/DateRangeFilter.vue";
	import SportsFilter from "@/Pages/Account/Statement/SportsFilter.vue";
	import Transactions from "@/Pages/Account/Statement/Transactions.vue";
	import TypesFilter from "@/Pages/Account/Statement/TypesFilter.vue";
	defineProps({
		transactions: Object,
	});
	const params = useUrlSearchParams("history");
	const clear = () => router.get(window.route("accounts.statement"));
	debouncedWatch(
		[
			() => params.search,
			() => params.time,
			() => params.from,
			() => params.to,
			() => params.types,
			() => params.sports,
			() => params.credit,
			() => params.debit,
		],
		([search, time, from, to, types, sports, credit, debit]) => {
			router.get(
				window.route("accounts.statement"),
				{
					...(search ? { search } : {}),
					...(time ? { time } : {}),
					...(from ? { from } : {}),
					...(to ? { to } : {}),
					...(types ? { types } : {}),
					...(sports ? { sports } : {}),
					...(credit ? { credit } : {}),
					...(debit ? { debit } : {}),
				},
				{
					preserveState: true,
					preserveScroll: true,
				},
			);
		},
		{
			debounce: 500,
		},
	);
</script>
<template>
	<FrontendLayout>
		<div class="px-3.5">
			<div class="grid sm:grid-cols-2 py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-gray-250 font-inter font-semibold">
					{{ $t("Statement") }}
				</h1>
				<div class="flex justify-end">
					<FormInput
						v-model="params.search"
						class="sm:max-w-xs w-full"
						size="sm">
						<template #lead>
							<VueIcon
								class="text-gray-400 w-4 h-4"
								:icon="HiSearch" />
						</template>
						<template #trail>
							<a
								@click.prevent="clear"
								class="text-gray-400 hover:text-red-500"
								href="#">
								<VueIcon class="w-4 h-4" :icon="HiSolidX" />
							</a>
						</template>
					</FormInput>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<div
						class="flex flex-col sm:flex-row gap-3 pb-4 border-b border-gray-200 dark:border-gray-650">
						<DateRangeFilter
							v-model:time="params.time"
							v-model:from="params.from"
							v-model:to="params.to" />
						<SportsFilter v-model:sports="params.sports" />
						<TypesFilter v-model:types="params.types" />
					</div>
					<div class="grid mt-4 sm:grid-cols-2 gap-5">
						<div
							class="flex flex-col gap-3 p-4 bg-gray-150 dark:bg-gray-750">
							<h3
								class="text-sm w-full pb-2 border-b border-gray-250 dark:border-gray-650">
								{{ $t("Filter Breakdown") }}
							</h3>
							<div class="flex items-center justify-between">
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										{{ $page.props.bets ?? 0 }}
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("bets") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										{{ $page.props.won ?? 0 }}
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("WON") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										{{ $page.props.lost ?? 0 }}
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("LOST") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										<MoneyFormat
											:amount="
												$page.props.profitLoss ?? 0
											" />
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("P&L") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										<MoneyFormat
											:amount="
												$page.props.exposure ?? 0
											" />
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("EXPOSURE") }}
									</h3>
								</div>
							</div>
						</div>

						<div
							class="flex flex-col gap-3 p-4 bg-gray-150 dark:bg-gray-750">
							<div
								class="w-full pb-2 border-b border-gray-250 dark:border-gray-650 flex items-center justify-between">
								<h3 class="text-sm">
									{{ $t("Referrals") }}
								</h3>
								<Link
									class="text-xs font-inter uppercase font-semibold text-sky-500 dark:text-sky-400 hover:text-sky-600 dark:hover:text-sky-300"
									:href="route('accounts.commission')">
									{{ $t("View Page") }}
								</Link>
							</div>
							<div class="flex items-center justify-between">
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										{{ $page.props.referrals ?? 0 }}
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("New Referrals") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										<MoneyFormat
											:amount="
												$page.props.refComMonth ?? 0
											" />
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("This Month") }}
									</h3>
								</div>
								<div class="text-center">
									<h3
										class="text-base font-inter text-gray-800 dark:text-gray-200">
										<MoneyFormat
											:amount="
												$page.props.refComLifetime ?? 0
											" />
									</h3>
									<h3 class="text-xs opacity-75 uppercase">
										{{ $t("Lifetime") }}
									</h3>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="p-4">
				<div class="flex justify-end space-x-3 my-4">
					<Switch v-model="params.debit">
						{{ $t("Debit") }}
					</Switch>
					<Switch v-model="params.credit">
						{{ $t("Credit") }}
					</Switch>
				</div>
				<Transactions />
			</div>
		</div>
	</FrontendLayout>
</template>
