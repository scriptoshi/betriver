<script setup>
	import { router } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiSolidChevronDown, HiSolidX } from "oh-vue-icons/icons";

	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import D3LineChart from "@/Pages/Admin/Dashboard/AccountsCharts/D3LineChart.vue";
	defineProps({ datum: Array });

	const config = {
		date: { key: "date", inputFormat: "%Y-%m-%d" },
		values: ["profit", "loss", "arbitrage", "fees"],
		tooltip: {
			labels: [
				"Bookie Profits",
				"Bookie Loss",
				"Exchange Arbitrage",
				"Exchange Fees",
			],
		},
		color: {
			key: false,
			keys: {
				profit: "#059669",
				loss: "#ef4444",
				arbitrage: "#3b82f6",
				fees: "#4f46e5",
			},
			scheme: false,
			current: "#1f77b4",
			default: "#AAA",
			axis: "#000",
		},
	};

	const timePeriodOptions = [
		{ name: "Last 24 Hours", value: "1day" },
		{ name: "Last 7 Days", value: "7days" },
		{ name: "Last 30 Days", value: "1month" },
		{ name: "Last 6 Months", value: "6months" },
		{ name: "Last Year", value: "1year" },
		{ name: "All Time", value: "lifetime" },
	];

	const params = useUrlSearchParams("history");

	debouncedWatch(
		() => params.chart,
		(chart) => {
			router.get(
				window.route("admin.dashboard"),
				{
					...(chart ? { chart } : {}),
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
	<div
		class="w-full shadow-sm rounded-sm p-4 bg-white dark:bg-gray-800 h-[400px]">
		<div
			class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
			<h3>Admin Earnings</h3>
			<div class="flex w-full sm:max-w-[160px] items-center justify-end">
				<Multiselect
					class="md"
					:options="timePeriodOptions"
					valueProp="value"
					label="name"
					:placeholder="$t('Period')"
					v-model="params.chart"
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
		</div>
		<D3LineChart :config="config" :datum="datum" />
	</div>
</template>
