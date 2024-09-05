<script setup>
	import { computed } from "vue";

	import { useForm, usePage } from "@inertiajs/vue3";
	import { useI18n } from "vue-i18n";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioCards from "@/Components/RadioCards.vue";
	import Switch from "@/Components/Switch.vue";
	import { Badge } from "@/Components/ui/badge";
	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import History from "@/Pages/Withdraws/History.vue";
	const props = defineProps({
		accounts: Array,
		gateways: Object,
		currencies: Object,
		auth: Object,
		withdraws: Object,
	});
	const form = useForm({
		tos: false,
		amount: 0,
		//
		account_id: null,
	});

	const selectedAccount = computed(() =>
		form.account_id
			? props.accounts.find((c) => c.id === form.account_id)
			: null,
	);
	const gateway = computed(
		() =>
			props.gateways[selectedAccount.value?.currency?.gateway?.gid] ??
			null,
	);
	const createWithdraw = () => {
		form.post(window.route("withdraws.store"));
	};
	const estimate = computed(() => {
		const fee = parseFloat(
			usePage().props.auth.user.levelConfig?.withdrawFees ?? 0,
		);
		if (parseFloat(form.amount) === 0) return 0;
		if (!selectedAccount.value?.currency) return null;
		const rate = selectedAccount.value?.currency?.rate;
		if (!rate) return null;
		const amount =
			fee > 0
				? parseFloat(form.amount) * ((100 - fee) / 100)
				: parseFloat(form.amount);
		return (amount / parseFloat(rate)).toFixed(8) * 1;
	});
	const { t } = useI18n();
	const terms = [
		t(
			"I confirm this withdrawal is accurate and authorize {site} to process it per the terms of service.",
			{ site: usePage().props.appName },
		),
		t(
			"By confirming, I acknowledge this withdrawal is final and subject to {site}'s processing times and fees.",
			{ site: usePage().props.appName },
		),
		t(
			"I certify that I own this account and authorize this withdrawal as per {site}'s policies.",
			{ site: usePage().props.appName },
		),
		t(
			"I understand this withdrawal may be reviewed and agree to cooperate with any required verification.",
		),
		t(
			"I confirm this withdrawal complies with all laws and {site}'s terms of service.",
			{ site: usePage().props.appName },
		),
	];
</script>

<template>
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-650 dark:text-white font-inter font-semibold">
					{{ $t("Withdraw money") }}
				</h1>
			</div>

			<div class="p-6 mt-4 bg-white dark:bg-gray-800 rounded shadow-md">
				<div
					v-if="accounts.length == 0"
					class="p-6 my-4 bg-gray-150 dark:bg-gray-750 rounded">
					<h3 class="text-lg mb-3">
						{{
							$t("You have not whitelisted any withdraw account.")
						}}
					</h3>
					<p class="mb-4">
						{{
							$t(
								"In order to withdraw your funds, you need to whitelist at least one withdraw account. Process is fast and takes no time.",
							)
						}}
					</p>
					<PrimaryButton link :href="route('whitelists.index')">
						{{ $t("Whitelist your withdraw account") }}
					</PrimaryButton>
				</div>
				<template v-else>
					<FormInput
						:label="$t('Amount to withdraw from your account')"
						class="mb-2 max-w-sm"
						:error="form.errors.amount"
						v-model="form.amount">
						<template #trail>
							<CurrencySymbol class="text-xs font-semibold" />
						</template>
					</FormInput>
					<div class="mb-4 flex items-center space-x-2">
						<a
							@click="form.amount = gateway?.min"
							v-if="gateway?.min > 0"
							href="#">
							<Badge
								variant="outline"
								class="!rounded hover:bg-gray-100 dark:hover:bg-gray-750 border-gray-250 dark:border-gray-550">
								Min {{ gateway?.min }}
								{{ $page.props.currency.currency_code }}
							</Badge>
						</a>
						<a
							@click="form.amount = gateway?.max"
							v-if="gateway?.max > 0"
							href="#">
							<Badge
								variant="outline"
								class="!rounded border-gray-250 hover:bg-gray-100 dark:hover:bg-gray-750 dark:border-gray-550">
								Max {{ gateway?.max }}
								{{ $page.props.currency.currency_code }}
							</Badge>
						</a>
					</div>
					<div class="p-6 my-4 bg-gray-150 dark:bg-gray-750 rounded">
						<FormLabel class="mb-3">
							{{ $t("Select your account") }}
						</FormLabel>
						<RadioCards
							v-model="form.account_id"
							:grid="3"
							:options="
								accounts.map((g) => ({
									key: g.id,
									subtitle: g.currency.name,
									title: g.currency.code,
									value: g.id,
									img: g.currency.logo_url,
								}))
							" />
						<p
							class="text-red-500 mt-2 dark:text-red-400 text-xs font-semibold"
							v-if="form.errors.currency_id">
							{{ form.errors.currency_id }}
						</p>
					</div>
					<CollapseTransition>
						<div v-show="!!selectedAccount">
							<div class="flex items-center space-x-3 mb-5">
								<img
									:src="
										selectedAccount?.currency?.gateway?.logo
									"
									class="w-7 h-7 rounded-full" />
								<h3 class="text-lg">
									Processed Via
									{{
										selectedAccount?.currency?.gateway?.name
									}}
								</h3>
							</div>
							<FormInput
								:label="
									$t(gateway?.destination ?? '', {
										symbol: selectedAccount?.name,
									})
								"
								class="mb-6 max-w-lg"
								disabled
								:help="
									$t(
										'Converted amount is an estimate. final withdrawal amount may differ based on gatewway',
									)
								"
								:modelValue="selectedAccount?.payout_address">
								<template #trail>
									<span class="text-xs font-semibold">
										{{ estimate
										}}{{ selectedAccount?.symbol }}
									</span>
								</template>
							</FormInput>
						</div>
					</CollapseTransition>
					<div
						class="p-4 mb-4 bg-white dark:bg-gray-750 rounded-lg shadow-md">
						<h2
							class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">
							Withdrawal Terms
						</h2>
						<div class="mb-4">
							<ul class="list-disc pl-5 space-y-2">
								<li
									v-for="(term, index) in terms"
									:key="index"
									class="text-sm text-gray-700 dark:text-gray-300">
									{{ term }}
								</li>
							</ul>
						</div>
						<div class="flex items-center mb-6">
							<Switch v-model="form.tos">
								{{ $t("I acknowledge these terms.") }}
							</Switch>
						</div>
					</div>

					<p
						v-if="gateway && !gateway?.enable_withdraw"
						class="text-red-600 mb-6 dark:text-red-400">
						{{
							$t(
								"{gateway} is currently offline, Please select another gateway",
								{ gateway: gateway?.label },
							)
						}}
					</p>
					<PrimaryButton
						@click="createWithdraw"
						:disabled="
							form.processing ||
							!gateway?.enable_withdraw ||
							!form.tos
						"
						class="text-xs font-semibold uppercase">
						{{ $t("Initiate Withdraw") }}
					</PrimaryButton>
				</template>
			</div>
			<History :withdraws="withdraws" />
		</div>
	</FrontendLayout>
</template>
