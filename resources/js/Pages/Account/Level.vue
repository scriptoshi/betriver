<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { Loader2 } from "lucide-vue-next";

	import { Badge } from "@/Components/ui/badge";
	import Button from "@/Components/ui/button/Button.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import LevelOneIcon from "@/Pages/Account/Level/LevelOneIcon.vue";
	import LevelThreeIcon from "@/Pages/Account/Level/LevelThreeIcon.vue";
	import LevelTwoIcon from "@/Pages/Account/Level/LevelTwoIcon.vue";
	import StatTile from "@/Pages/Account/Level/StatTile.vue";
	defineProps({
		levelOne: Object,
		levelTwo: Object,
		levelThree: Object,
		bets: Object,
		amounts: Object,
		profitLoss: Object,
	});
	const optinForm = useForm({ level: null });
	const optinFor = (level) => {
		optinForm.level = level;
		optinForm.post(window.route("accounts.optin"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-gray-250 font-inter font-semibold">
					{{ $t("Commission Tier") }}
				</h1>
				<p>
					{{
						$t(
							"Below you can see which commission tier your account is on, as well as how many bets and the amount you have staked this calendar month.",
						)
					}}
				</p>
			</div>
			<h3
				class="text-base font-inter font-extrabold mb-6 text-gray-900 dark:text-gray-50">
				{{ $t("Your Tier (Level)") }}
			</h3>
			<div
				class="bg-gray-50 dark:bg-gray-850 rounded-[4px] border-sky-600 border dark:border-sky-400">
				<div class="p-6">
					<div class="flex items-center justify-start space-x-4">
						<LevelOneIcon
							v-if="$page.props.auth.user.isLevelOne"
							class="text-gray-800 dark:text-white w-14 h-14" />
						<LevelTwoIcon
							v-if="$page.props.auth.user.isLevelTwo"
							class="text-gray-800 dark:text-white w-14 h-14" />
						<LevelThreeIcon
							v-if="$page.props.auth.user.isLevelThree"
							class="text-gray-800 dark:text-white w-14 h-14" />
						<div>
							<h3
								class="text-gray-800 dark:text-white text-base font-inter">
								{{ $page.props.auth.user.levelConfig.name }}
							</h3>
							<p>
								{{
									$page.props.auth.user.levelConfig
										.description
								}}
							</p>
							<h3
								class="text-gray-600 dark:text-gray-200 text-sm font-semibold">
								{{ $page.props.auth.user.levelConfig.limits }}
							</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="grid mt-3 gap-4 sm:grid-cols-3">
				<StatTile :stat="bets" />
				<StatTile is-money :stat="amounts" />
				<StatTile is-money :stat="profitLoss" />
			</div>
			<h3
				class="text-base font-inter font-extrabold my-6 text-gray-900 dark:text-gray-50">
				{{ $t("Other Commission Tiers") }}
			</h3>
			<div class="bg-gray-50 mt-6 dark:bg-gray-850 rounded-[4px]">
				<div class="p-6">
					<div class="flex items-center justify-start space-x-4">
						<LevelOneIcon
							class="text-gray-800 dark:text-white w-14 h-14" />
						<div>
							<h3
								class="text-gray-800 dark:text-white text-base font-inter">
								{{ levelOne.name }}
							</h3>
							<p>{{ levelOne.description }}</p>
							<h3
								class="text-gray-600 dark:text-gray-200 text-sm font-semibold">
								{{ levelOne.limits }}
							</h3>
							<Badge
								v-if="
									$page.props.auth.user
										.requested_next_level == 1
								"
								class="mt-4"
								variant="destructive">
								Request Pending
							</Badge>
							<Button
								v-else-if="!$page.props.auth.user.isLevelOne"
								class="mt-4 text-gray-800 dark:text-white"
								variant="outline"
								@click="optinFor(1)"
								:disabled="
									optinForm.processing && optinForm.level == 1
								"
								size="sm">
								<Loader2
									v-if="
										optinForm.processing &&
										optinForm.level == 1
									"
									class="w-4 h-4 -ml-1 mr-2 animate-spin" />
								{{ $t("Optin for") }} {{ levelOne.name }}
							</Button>
						</div>
					</div>
				</div>
			</div>
			<div class="bg-gray-50 mt-3 dark:bg-gray-850 rounded-[4px]">
				<div class="p-6">
					<div class="flex items-center justify-start space-x-4">
						<LevelTwoIcon
							class="text-gray-800 dark:text-white w-14 h-14" />
						<div>
							<h3
								class="text-gray-800 dark:text-white text-base font-inter">
								{{ levelTwo.name }}
							</h3>
							<p>{{ levelTwo.description }}</p>
							<h3
								class="text-gray-600 dark:text-gray-200 text-sm font-semibold">
								{{ levelTwo.limits }}
							</h3>
							<Badge
								v-if="
									$page.props.auth.user
										.requested_next_level == 2
								"
								class="mt-4"
								variant="destructive">
								Request Pending
							</Badge>
							<Button
								v-else-if="!$page.props.auth.user.isLevelTwo"
								class="mt-4 text-gray-800 dark:text-white"
								variant="outline"
								@click="optinFor(2)"
								:disabled="
									optinForm.processing && optinForm.level == 2
								"
								size="sm">
								<Loader2
									v-if="
										optinForm.processing &&
										optinForm.level == 2
									"
									class="w-4 h-4 -ml-1 mr-2 animate-spin" />
								{{ $t("Optin for") }} {{ levelTwo.name }}
							</Button>
						</div>
					</div>
				</div>
			</div>
			<div class="bg-gray-50 mt-3 dark:bg-gray-850 rounded-[4px]">
				<div class="p-6">
					<div class="flex items-center justify-start space-x-4">
						<LevelThreeIcon
							class="text-gray-800 dark:text-white w-14 h-14" />
						<div>
							<h3
								class="text-gray-800 dark:text-white text-base font-inter">
								{{ levelThree.name }}
							</h3>
							<p>{{ levelThree.description }}</p>
							<h3
								class="text-gray-600 dark:text-gray-200 text-sm font-semibold">
								{{ levelThree.limits }}
							</h3>
							<Badge
								v-if="
									$page.props.auth.user
										.requested_next_level == 3
								"
								class="mt-4"
								variant="destructive">
								Request Pending
							</Badge>
							<Button
								v-else-if="!$page.props.auth.user.isLevelThree"
								class="mt-4 text-gray-800 dark:text-white"
								variant="outline"
								@click="optinFor(3)"
								:disabled="
									optinForm.processing && optinForm.level == 3
								"
								size="sm">
								<Loader2
									v-if="
										optinForm.processing &&
										optinForm.level == 3
									"
									class="w-4 h-4 -ml-1 mr-2 animate-spin" />
								{{ $t("Optin for") }} {{ levelThree.name }}
							</Button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</FrontendLayout>
</template>
