<script setup>
	import { computed, watch } from "vue";

	import { useForm } from "@inertiajs/vue3";

	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioCards from "@/Components/RadioCards.vue";
	import Switch from "@/Components/Switch.vue";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import History from "@/Pages/Deposits/History.vue";
	const props = defineProps({
		gateways: Array,
		auth: Object,
		deposits: Object,
		currencies: Object,
	});
	const form = useForm({
		tos: false,
		amount: 0,
		currency: null,
		gateway: Object.values(props.gateways)[0].value,
	});
	const gateway = computed(() => props.gateways[form.gateway] ?? null);
	const activeCurrencies = computed(
		() => props.currencies[form.gateway] ?? [],
	);
	watch(gateway, () => (form.currency = null));
	const createDeposit = () => {
		form.post(window.route("deposits.store"));
	};
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-white font-inter font-semibold">
					{{ $t("Deposit money") }}
				</h1>
			</div>
			<RadioCards v-model="form.gateway" :options="gateways" />

			<div class="p-6 mt-4 bg-white dark:bg-gray-800 rounded shadow-md">
				<FormInput
					:label="$t('Amount to deposit to your account')"
					class="mb-6 max-w-sm"
					:error="form.errors.amount"
					v-model="form.amount">
					<template #trail>
						<CurrencySymbol class="text-xs font-semibold" />
					</template>
				</FormInput>
				<div class="p-6 my-4 bg-gray-250 dark:bg-gray-750 rounded">
					<FormLabel class="mb-3">
						The currency you wish to deposit
					</FormLabel>
					<RadioCards
						v-model="form.currency"
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
				<h2
					class="text-lg font-bold mb-4 text-gray-800 dark:text-white">
					Terms
				</h2>
				<p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
					By initiating a deposit, you confirm that you are at least
					18 years old and that the funds are legally obtained. You
					agree to use the deposited funds solely for betting
					activities on this site, in compliance with our full terms
					of service and applicable laws.
				</p>
				<div class="flex items-center mb-6">
					<Switch v-model="form.tos">
						{{ $t("I acknowledge these terms.") }}
					</Switch>
				</div>
				<p
					v-if="!gateway.enable_deposit"
					class="text-red-600 mb-6 dark:text-red-400">
					{{
						$t(
							"{gateway} is currently offline, Please select another gateway",
							{ gateway: gateway.label },
						)
					}}
				</p>
				<PrimaryButton
					@click="createDeposit"
					:disabled="
						form.processing ||
						!gateway.enable_deposit ||
						!form.tos ||
						!form.gateway
					"
					class="text-xs font-semibold uppercase">
					{{ $t("Initiate Deposit") }}
				</PrimaryButton>
			</div>
			<History :deposits="deposits" />
		</div>
	</FrontendLayout>
</template>
