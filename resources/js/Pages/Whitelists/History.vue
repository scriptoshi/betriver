<script setup>
	import { ref } from "vue";

	import { router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { Search } from "lucide-vue-next";
	import { HiTrash } from "oh-vue-icons/icons";

	import AdminTableLink from "@/Components/AdminTableLink.vue";
	import Flag from "@/Components/Flag";
	import FormInput from "@/Components/FormInput.vue";
	import Pagination from "@/Components/Pagination.vue";
	import { Badge } from "@/Components/ui/badge";
	import VueIcon from "@/Components/VueIcon.vue";
	import WeCopy from "@/Components/WeCopy.vue";
	defineProps({
		whitelists: Object,
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteWhitelistForm = useForm({});

	const deleteWhitelist = (whitelist) => {
		deleteWhitelistForm.delete(
			window.route("whitelists.destroy", { whitelist: whitelist.uuid }),
			{
				preserveScroll: true,
				preserveState: true,
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("whitelists.index"),
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
	<div class="my-6 pt-8">
		<div class="flex flex-col sm:flex-row mb-1 gap-4 sm:justify-between">
			<h3>{{ $t("Current accounts") }}</h3>
			<FormInput
				:placeholder="$t('Search accounts')"
				class="max-w-xs w-full">
				<template #lead>
					<Search class="w-4 h-4 text-gray-400" />
				</template>
			</FormInput>
		</div>
		<div class="overflow-x-auto">
			<table
				class="table-default table-hover border-separate border-spacing-x-0 border-spacing-y-3"
				role="table">
				<thead class="hidden">
					<tr role="row">
						<th role="columnheader">
							{{ $t("WID") }}
						</th>

						<th role="columnheader">
							{{ $t("Gateway") }}
						</th>

						<th
							scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $t("Address") }}
						</th>
						<th
							scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							{{ $t("Status") }}
						</th>
					</tr>
				</thead>
				<tbody role="rowgroup">
					<tr
						v-for="whitelist in whitelists.data"
						:key="whitelist.id"
						role="row">
						<td
							class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div class="flex items-center">
								<img
									class="w-7 h-7 rounded-full mr-3"
									:src="whitelist.currency.gateway.logo" />
								<div>
									<div class="">
										{{ whitelist.currency.gateway.name }}
									</div>
									<div
										class="text-[10px] font-semibold text-gray-400">
										#{{ whitelist.uid }}
									</div>
								</div>
							</div>
						</td>
						<td
							class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div>
								<div class="flex items-center">
									<Flag
										class="w-7 h-7 rounded-full mr-3"
										:iso="whitelist.currency.logo_url"
										v-if="
											whitelist.currency.logo_url
												.length == 3
										" />
									<img
										v-else
										class="w-7 h-7 rounded-full mr-3"
										:src="whitelist.currency.logo_url" />
									<div>
										<div class="">
											{{ whitelist.currency.name }}
										</div>
										<div
											class="text-[10px] font-semibold text-gray-400">
											<WeCopy
												after
												:text="
													whitelist.payout_address
												">
												{{ whitelist.payout_address }}
											</WeCopy>
										</div>
									</div>
								</div>
							</div>
						</td>

						<td
							class="px-6 py-4 text-right whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
							<div class="flex items-center space-x-3">
								<Badge
									variant="outline"
									class="uppercase font-semibold font-inter"
									:class="{
										'border-gray-400 dark:border-gray-650 text-gray-600 dark:text-gray-400':
											[
												'review',
												'pending',
												'processing',
											].includes(whitelist.status),
										'dark:border-green-400 border-green-600 text-green-600 dark:text-green-400':
											['approved', 'complete'].includes(
												whitelist.status,
											),
										'dark:border-red-400 border-red-600 text-red-600 dark:text-red-400':
											['rejected', 'failed'].includes(
												whitelist.status,
											),
									}">
									{{ whitelist.status }}
								</Badge>
								<AdminTableLink
									@click.prevent="deleteWhitelist(whitelist)"
									href="#"
									v-tippy="$t('Request removal')">
									<VueIcon
										:icon="HiTrash"
										class="w-4 h-5 text-gray-600 hover:text-red-600 dark:hover:text-red-400 dark:text-gray-300" />
								</AdminTableLink>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<Pagination :meta="whitelists.meta" />
	</div>
</template>
