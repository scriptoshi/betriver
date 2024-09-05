<script setup>
	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";

	const props = defineProps({
		personal: Object,
	});
	const form = useForm({
		stake_limit: props.personal.stake_limit,
	});

	const updateGrossLimit = () => {
		form.put(window.route("personal.limit.stake"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
		<header>
			<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Limit your stake
			</h2>
			<p class="text-sm text-gray-600 dark:text-gray-400">
				Set a limit on the maximum amount you can stake on a bet.
			</p>
			<p class="text-sm text-gray-600 dark:text-gray-400">
				To remove your stake limit, or to change your stake limit to a
				higher amount, you will need to wait a 7-day cooling off period.
			</p>
		</header>
		<div class="grid mt-8">
			<FormInput
				class="max-w-xs"
				v-model="form.stake_limit"
				:error="form.errors.stake_limit"
				:disabled="form.processing || !personal.canUpdateStake"
				:help="
					personal.nextStakeLimitAt
						? `Can change ${personal.nextStakeLimitAt}`
						: null
				"
				label="Maximum per stake">
				<template #trail>
					<CurrencySymbol class="text-xs font-semibold" />
				</template>
			</FormInput>
		</div>
		<div class="mt-4">
			<CollapseTransition>
				<p
					v-show="form.recentlySuccessful"
					class="mb-3 text-green-500 dark:text-green-400">
					Saved successfully
				</p>
			</CollapseTransition>
			<PrimaryButton
				@click="updateGrossLimit"
				:disabled="form.processing || !personal.canUpdateStake"
				class="text-xs font-semibold uppercase">
				<Loading v-if="form.processing" class="!w-4 !h-4 mr-2 -ml-1" />
				Update Stake Limits
			</PrimaryButton>
		</div>
	</div>
</template>
