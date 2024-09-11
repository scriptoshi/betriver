<script setup>
	import { Link } from "@inertiajs/vue3";

	import MoneyFormat from "@/Components/MoneyFormat.vue";

	const headers = ["Name", "Joined", "email", "Balance"];

	defineProps({
		users: Array,
	});
</script>
<template>
	<div
		class="bg-white font-inter font-normal dark:bg-gray-800 shadow-md rounded-sm overflow-hidden">
		<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
			<h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
				Latest User Accounts
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
						v-for="user in users"
						:key="user.id">
						<td
							class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
							{{ user.name }}
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							{{ user.joined }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm">
							{{ user.email }}
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
							<MoneyFormat :amount="user.balance" />
						</td>
						<td
							class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
							<Link
								:href="
									route('admin.users.show', { user: user.id })
								"
								class="font-medium">
								View
							</Link>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
			<Link
				:href="route('admin.users.index')"
				class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
				View all users →
			</Link>
		</div>
	</div>
</template>
