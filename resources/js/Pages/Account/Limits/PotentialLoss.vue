<script setup>
	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";

	const props = defineProps({
		personal: Object,
		lossOptions: Object,
	});
	const form = useForm({
		loss_limit_interval: props.personal.loss_limit_interval,
		loss_limit: props.personal.loss_limit,
	});

	const updateGrossLimit = () => {
		form.put(window.route("personal.limit.loss"), {
			preserveScroll: true,
		});
	};
</script>

<template>
	<div class="p-4 sm:p-8 bg-white dark:bg-gray-850 sm:rounded">
		<header>
			<h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
				Limit your potential loss
			</h2>
			<p class="text-sm text-gray-600 dark:text-gray-400">
				Set a limit on the amount you can lose for a period of your
				choice.
			</p>
		</header>

		<div class="grid mt-8 gap-6">
			<div>
				<FormLabel class="mb-2">Loss Interval</FormLabel>
				<RadioSelect
					v-model="form.loss_limit_interval"
					:options="lossOptions"
					class="gap-3" />
			</div>
			<FormInput
				class="max-w-xs"
				v-model="form.loss_limit"
				label="Loss Limit">
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
				Update Loss Limit
			</PrimaryButton>
		</div>
	</div>
</template>
