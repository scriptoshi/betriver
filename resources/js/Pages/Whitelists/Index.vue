<script setup>
	import { computed, watch } from "vue";

	import { useForm } from "@inertiajs/vue3";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormInput from "@/Components/FormInput.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioCards from "@/Components/RadioCards.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import History from "@/Pages/Whitelists/History.vue";
	const props = defineProps({
		gateways: Array,
		currencies: Object,
		auth: Object,
		whitelists: Object,
	});
	const form = useForm({
		currency_id: null,
		payout_address: null,
		gateway: Object.values(props.gateways)[0].value,
	});
	const gateway = computed(() => props.gateways[form.gateway] ?? null);
	const activeCurrencies = computed(() =>
		form.gateway ? props.currencies[form.gateway] ?? [] : [],
	);
	const selectedCurrency = computed(() =>
		form.currency_id
			? activeCurrencies.value?.find((c) => c.id === form.currency_id)
			: null,
	);
	watch(gateway, () => {
		form.currency_id = null;
		form.payout_address = null;
	});
	const createWhitelist = () => {
		form.post(window.route("whitelists.store"));
	};
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-white font-inter font-semibold">
					{{ $t("Whitelist payout accounts") }}
				</h1>
				<p class="text-sm">
					{{
						$t(
							"You need to approve withdrawal accounts before you can use them",
						)
					}}
				</p>
			</div>
			<RadioCards v-model="form.gateway" :options="gateways" />
			<div class="p-6 mt-4 bg-white dark:bg-gray-800 rounded shadow-md">
				<div class="p-6 my-4 bg-gray-100 dark:bg-gray-750 rounded">
					<FormLabel class="mb-3 text-gray-700 dark:text-gray-300">
						The currency you wish recieve payout in.
					</FormLabel>
					<RadioCards
						v-model="form.currency_id"
						:grid="3"
						:options="
							activeCurrencies.map((g) => ({
								key: g.id,
								subtitle: g.name,
								title: g.code,
								value: g.id,
								img: g.logo_url,
							}))
						" />
					<p
						class="text-red-500 mt-2 dark:text-red-400 text-xs font-semibold"
						v-if="form.errors.currency">
						{{ form.errors.currency }}
					</p>
				</div>
				<CollapseTransition>
					<FormInput
						v-show="!!selectedCurrency"
						:label="
							$t(gateway.destination ?? '', {
								symbol: selectedCurrency?.name,
							})
						"
						class="mb-6 max-w-lg"
						:error="form.errors.destination"
						:help="
							$t(
								'Crosscheck destination account before submitting.',
							)
						"
						v-model="form.payout_address"></FormInput>
				</CollapseTransition>

				<PrimaryButton
					@click="createWhitelist"
					:disabled="
						form.processing ||
						!form.gateway ||
						!form.currency_id ||
						!form.payout_address
					"
					class="text-xs font-semibold uppercase">
					{{ $t("Whitelist Payout destination") }}
				</PrimaryButton>
			</div>
			<History :whitelists="whitelists" />
		</div>
	</FrontendLayout>
</template>
