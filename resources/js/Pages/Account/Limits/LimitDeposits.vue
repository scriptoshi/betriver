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
		daily_gross_deposit: props.personal.daily_gross_deposit,
		weekly_gross_deposit: props.personal.weekly_gross_deposit,
		monthly_gross_deposit: props.personal.monthly_gross_deposit,
	});

	const updateGrossLimit = () => {
		form.put(window.route("personal.limit.deposit"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
		<header>
			<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Limit your gross deposits
			</h2>
			<p class="text-sm text-gray-600 dark:text-gray-400">
				Set a limit on the gross amount you can deposit for a period of
				your choice.
			</p>
		</header>
		<div class="grid mt-8 gap-4 sm:grid-cols-4">
			<FormInput v-model="form.daily_gross_deposit" label="Daily Limit">
				<template #trail>
					<CurrencySymbol class="text-xs font-semibold" />
				</template>
			</FormInput>
			<FormInput v-model="form.weekly_gross_deposit" label="Weekly Limit">
				<template #trail>
					<CurrencySymbol class="text-xs font-semibold" />
				</template>
			</FormInput>
			<FormInput
				v-model="form.monthly_gross_deposit"
				label="Monthly Limit">
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
				:disabled="form.processing"
				class="text-xs font-semibold uppercase">
				<Loading v-if="form.processing" class="!w-4 !h-4 mr-2 -ml-1" />
				Update your Limits
			</PrimaryButton>
		</div>
	</div>
</template>
