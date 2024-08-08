<script setup>
	import { ref } from "vue";

	const headers = ["Type", "Date", "Account", "Amount"];

	const orders = ref([
		{
			product: "iPhone 11 Pro",
			date: "today",
			status: "Pending..",
			price: 1199.99,
		},
		{
			product: 'MacBook Pro 15"',
			date: "today",
			status: "Pending..",
			price: 2299.0,
		},
		{
			product: "Nvidia GTX 2080 Ti",
			date: "today",
			status: "Pending..",
			price: 1200.0,
		},
		{
			product: "Playstation 4 Pro",
			date: "today",
			status: "Canceled",
			price: 399.0,
		},
		{
			product: "Nintendo Switch",
			date: "yesterday",
			status: "Completed",
			price: 349.0,
		},
		{
			product: "iPhone 11",
			date: "yesterday",
			status: "Completed",
			price: 999.0,
		},
		{
			product: "Airpods Pro",
			date: "yesterday",
			status: "Completed",
			price: 39.99,
		},
		{
			product: "Xbox One X",
			date: "yesterday",
			status: "Completed",
			price: 499.0,
		},
	]);

	const getStatusClass = (status) => {
		switch (status) {
			case "Pending..":
				return "text-yellow-600 dark:text-yellow-400";
			case "Canceled":
				return "text-red-600 dark:text-red-400";
			case "Completed":
				return "text-green-600 dark:text-green-400";
			default:
				return "text-gray-600 dark:text-gray-400";
		}
	};

	const formatPrice = (price) => {
		return `$${price.toFixed(2)}`;
	};
</script>
<template>
	<div
		class="bg-white font-inter font-normal dark:bg-gray-800 shadow-md rounded-sm overflow-hidden">
		<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
			<h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
				Latest Admin Accounts
			</h2>
		</div>
		<div class="overflow-x-auto">
			<table class="min-w-full dark:divide-gray-700">
				<thead class="bg-gray-50 dark:bg-gray-700">
					<tr>
						<th
							v-for="header in headers"
							:key="header"
							class="px-6 font-semibold py-3 text-left text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">
							{{ header }}
						</th>
						<th
							class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
							Action
						</th>
					</tr>
				</thead>
				<tbody class="bg-white dark:bg-gray-800 dark:divide-gray-700">
					<tr
						class="even:bg-gray-100 dark:even:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-900"
						v-for="order in orders"
						:key="order.product">
						<td
							class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300 dark:text-gray-200">
							{{ order.product }}
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ order.date }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							<span :class="getStatusClass(order.status)">
								{{ order.status }}
							</span>
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ formatPrice(order.price) }}
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
							<a href="#" class="font-medium">View</a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
			<a
				href="#"
				class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
				View all orders →
			</a>
		</div>
	</div>
</template>
