<script setup>
	import WeCopy from "@/Components/WeCopy.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import StatTile from "@/Pages/Account/Level/StatTile.vue";
	defineProps({
		referrals: Object,
		earnings: Object,
		lifeTime: Object,
		levels: Object,
		direct: Object,
	});
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-gray-250 font-inter font-semibold">
					{{ $t("Referral Earnings") }}
				</h1>
				<p>
					{{
						$t(
							"Below you can view and monitor your referral activity and earnings",
						)
					}}
				</p>
			</div>
			<h3
				class="text-base font-inter font-extrabold my-6 text-gray-900 dark:text-gray-50">
				{{ $t("Your Referral Id. Share this link to earn") }}
			</h3>
			<div
				class="py-2 px-4 text-gray-400 flex flex-col sm:flex-row sm:justify-between rounded border border-dashed border-gray-350 dark:border-gray-750 bg-white dark:bg-gray-850/50">
				<WeCopy
					after
					:text="route('home', { ref: $page.props.auth.user.refId })">
					{{ route("home", { ref: $page.props.auth.user.refId }) }}
				</WeCopy>
				<span>{{ direct }} {{ $t("Direct Referrals") }}</span>
			</div>
			<div class="grid mt-3 gap-4 sm:grid-cols-3">
				<StatTile :stat="referrals" />
				<StatTile is-money :stat="earnings" />
				<StatTile is-money :stat="lifeTime" />
			</div>
			<h3
				class="text-base font-inter font-extrabold mt-8 text-gray-900 dark:text-gray-50">
				{{ $t("Referral Commission Percent") }}
			</h3>
			<p class="mb-4">
				As a Percentage of Admin fees (Depending on user tier)
			</p>

			<div>
				<div class="overflow-x-auto">
					<table
						class="table-default table-hover border-separate border-spacing-x-0 border-spacing-y-3"
						role="table">
						<tbody role="rowgroup">
							<tr
								v-for="(level, i) in levels"
								:key="i"
								role="row">
								<td
									class="rounded-l-[4px] px-6 py-4 whitespace-nowrap bg-white dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-gray-300">
									Level #{{ level[0].level }}
								</td>
								<td
									v-for="(lvl, i) in level"
									:key="i"
									class="px-6 py-4 uppercase text-xs whitespace-nowrap bg-white dark:bg-gray-800 font-medium text-gray-900 dark:text-gray-300">
									{{ lvl.type }} :
									<span
										class="font-semibold text-gray-700 dark:text-gray-100 font-inter ml-1">
										{{ lvl.percent * 1 }}
									</span>
									%
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</FrontendLayout>
</template>
