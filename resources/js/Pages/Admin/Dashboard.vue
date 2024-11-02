<!-- Dashboard.vue -->

<script setup>
	import { computed } from "vue";

	import {
		ArrowUpIcon,
		CreditCardIcon,
		ShoppingCartIcon,
		UsersIcon,
	} from "@heroicons/vue/24/solid";
	import { OiHome } from "oh-vue-icons/icons";
	import { uid } from "uid";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { useBillions } from "@/hooks";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import AccountsCharts from "@/Pages/Admin/Dashboard/AccountsCharts.vue";
	import AdminAccounts from "@/Pages/Admin/Dashboard/AdminAccounts.vue";
	import StatsCard from "@/Pages/Admin/Dashboard/StatsCard.vue";

	const props = defineProps({
		totalUsers: {
			type: Number,
			required: true,
		},
		totalTickets: {
			type: Number,
			required: true,
		},
		totalSlips: {
			type: Number,
			required: true,
		},
		usersRegisteredToday: {
			type: Object,
			required: true,
			validator: (value) => {
				return "value" in value && "percentage" in value;
			},
		},
		activeBettors: {
			type: Object,
			required: true,
			validator: (value) => {
				return "value" in value && "percentage" in value;
			},
		},
		liveGamesToday: {
			type: Object,
			required: true,
			validator: (value) => {
				return "value" in value && "percentage" in value;
			},
		},
		commissionCollected: {
			type: Object,
			required: true,
			validator: (value) => {
				return "value" in value && "percentage" in value;
			},
		},
		arbitrage: {
			type: Number,
			required: true,
		},
		houseWins: {
			type: Number,
			required: true,
		},
		losses: {
			type: Number,
			required: true,
		},
		profit: {
			type: Number,
			required: true,
		},
		totalBookieBets: {
			type: Number,
			required: true,
		},
		totalExchangeBets: {
			type: Number,
			required: true,
		},
		chartData: Array,
		latest: Array,
	});

	const statistics = computed(() => [
		{ uid: uid(), name: "House Wins", stat: props.houseWins },
		{ uid: uid(), name: "Arbitrage", stat: props.arbitrage },
		{ uid: uid(), name: "Losses", stat: props.losses },
		{ uid: uid(), name: "Profit", stat: props.profit },
		{ uid: uid(), name: "Bookie", stat: props.totalBookieBets },
		{ uid: uid(), name: "Exchange", stat: props.totalExchangeBets },
	]);

	const statsCardData = computed(() => [
		{
			icon: UsersIcon,
			iconBackgroundColor: "bg-blue-500",
			value: props.usersRegisteredToday.value,
			label: "Users Registered Today",
			change: props.usersRegisteredToday.percentage,
			link: window.route("admin.users.index"),
			linkText: "View all users",
		},
		{
			icon: ArrowUpIcon,
			iconBackgroundColor: "bg-green-500",
			value: props.activeBettors.value,
			label: "Active Bettors",
			change: props.activeBettors.percentage,
			link: window.route("admin.trades.index"),
			linkText: "Explore Trades",
		},
		{
			icon: ShoppingCartIcon,
			iconBackgroundColor: "bg-yellow-500",
			value: props.liveGamesToday.value,
			label: "Live Games Today",
			change: props.liveGamesToday.percentage,
			link: window.route("admin.games.index"),
			linkText: "View all Games",
		},
		{
			icon: CreditCardIcon,
			iconBackgroundColor: "bg-purple-500",
			value: props.commissionCollected.value,
			label: "Commission Collected",
			change: props.commissionCollected.percentage,
			link: window.route("admin.transactions.index"),
			linkText: "View Transactions",
			isMoney: true,
		},
	]);
</script>
<template>
	<AdminLayout>
		<div class="container py-8">
			<div class="grid sm:grid-cols-2">
				<div>
					<div class="flex items-center space-x-1">
						<VueIcon
							:icon="OiHome"
							class="w-6 h-6 text-emerald-500" />
						<h1
							class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
							Dashboard
						</h1>
					</div>
					<p class="text-gray-600 dark:text-gray-300 mb-4">
						Welcome, admin! You have 8 new registrations today.
					</p>
				</div>
				<div class="flex justify-end items-center">
					<div class="px-2 sm:px-5 text-center">
						<p class="text-2xl text-dark">
							{{ useBillions(totalUsers) }}
						</p>
						<p class="text-gray-400">Total Users</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<p class="text-2xl text-dark">
							{{ useBillions(totalTickets) }}
						</p>
						<p class="text-gray-400">Total Tickets</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<p class="text-2xl text-dark">
							{{ useBillions(totalSlips) }}
						</p>
						<p class="text-gray-400">Total Bets</p>
					</div>
				</div>
			</div>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
				<StatsCard
					v-for="(stat, index) in statsCardData"
					:key="index"
					v-bind="stat" />
			</div>
			<div
				class="grid rounde-sm mt-8 sm:grid-cols-3 md:grid-cols-6 bg-white p-8 dark:bg-gray-800 transition-all w-full h-full">
				<div
					v-for="stat in statistics"
					:key="stat.uid"
					class="text-center">
					<div
						class="uppercase text-gray-800 dark:text-gray-100 tracking-widest text-lg">
						{{ stat.name }}
					</div>
					<div
						class="font-inter font-bold text-2xl text-emerald-600 dark:text-emerald-300">
						<MoneyFormat :amount="stat.stat" />
					</div>
				</div>
			</div>
			<AccountsCharts :datum="chartData" class="mt-4" />
			<AdminAccounts :users="latest" class="mt-4" />
		</div>
	</AdminLayout>
</template>
