<script setup>
	import { computed, onMounted, onUnmounted, ref, watch } from "vue";

	import { router, useForm } from "@inertiajs/vue3";
	import { RefreshCw } from "lucide-vue-next";

	import BreadCrumbs from "@/Components/BreadCrumbs.vue";
	import StakeSidebarCard from "@/Components/Cards/StakeSidebarCard.vue";
	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import AppLayout from "@/Layouts/FrontendLayout.vue";
	import MarketButtons from "@/Pages/Stakes/MarketButtons.vue";

	const props = defineProps({
		odds: { type: Array, default: null },
		stake: { type: Object, default: null },
		auth: { type: Object, default: null },
	});

	const calculationType = ref(
		props.stake
			? props.stake.type === "back"
				? "backing"
				: "laying"
			: "laying",
	);

	const partialTradeOut = ref(100);
	if (props.stake) {
		const filledAmount = parseFloat(props.stake.filled) || 0;
		const totalAmount = parseFloat(props.stake.amount) || 0;
		partialTradeOut.value =
			totalAmount > 0 ? (filledAmount / totalAmount) * 100 : 0;
	}

	const fields = ref([
		{
			id: "stake",
			label:
				calculationType.value === "laying" ? "LAY STAKE" : "BACK STAKE",
			value: props.stake ? props.stake.amount : 10,
			disabled: !!props.stake,
			trail: "money",
		},
		{
			id: "initialOdds",
			label: "INITIAL ODDS",
			value: props.stake ? props.stake.odds : 3.5,
			disabled: !!props.stake,
		},
		{
			id: "newOdds",
			label: "NEW ODDS",
			value: props.stake ? props.stake.odds : 3.5,
			disabled: false,
			autofocus: true,
		},
		{
			id: "commission",
			label: "COMMISSION",
			value: props.auth.user?.levelConfig?.commissionProfit ?? 2,
			disabled: !!props.auth?.user,
			trail: "%",
		},
	]);

	watch(
		() => calculationType.value,
		(newType) => {
			fields.value[0].label =
				newType === "laying" ? "LAY STAKE" : "BACK STAKE";
		},
	);

	const setOdds = (val) => {
		fields.value[2].value = parseFloat(val);
	};

	const results = computed(() => {
		const stake = parseFloat(fields.value[0].value);
		const initialOdds = parseFloat(fields.value[1].value);
		const newOdds = parseFloat(fields.value[2].value);
		const commission = parseFloat(fields.value[3].value) / 100;

		let liability, newStake, profit, commissionPaid;

		if (calculationType.value === "laying") {
			liability = stake * (initialOdds - 1);
			newStake = liability / (newOdds - 1);
			profit = stake - newStake;
		} else {
			liability = stake;
			newStake = (stake * initialOdds) / newOdds;
			profit = newStake - stake;
		}

		// Apply commission
		commissionPaid = Math.max(profit, 0) * commission;
		profit -= commissionPaid;

		// Apply partial trade-out
		const partialFactor = partialTradeOut.value / 100;
		newStake *= partialFactor;
		profit *= partialFactor;
		commissionPaid *= partialFactor;
		liability *= partialFactor;

		return [
			{
				label:
					calculationType.value === "laying"
						? "YOU SHOULD BACK"
						: "YOU SHOULD LAY",
				value: newStake.toFixed(2),
			},
			{
				label: "LIABILITY",
				value: liability.toFixed(2),
			},
			{ label: "PROFIT/LOSS", value: profit.toFixed(2) },
			{
				label: "COMMISSION PAID",
				value: commissionPaid > 0 ? commissionPaid.toFixed(2) : 0,
			},
		];
	});

	const crumbs = [
		{ name: "Home", href: "/" },
		{ name: "Tradeout", href: null },
	];

	const reloading = ref(false);
	const reload = () => {
		reloading.value = true;
		router.reload({
			only: ["odds"],
			onFinish() {
				reloading.value = false;
			},
		});
	};

	// Polling function
	let pollInterval;
	const startPolling = () => {
		pollInterval = setInterval(() => {
			reload();
		}, 60000); // 60 seconds
	};

	const stopPolling = () => {
		clearInterval(pollInterval);
	};

	// Lifecycle hooks
	onMounted(() => {
		startPolling();
	});

	onUnmounted(() => {
		stopPolling();
	});
	const form = useForm({ new_odds: 0, stake_id: props.stake.id });
	const tradeOut = () => {
		form.transform((data) => ({
			...data,
			new_odds: parseFloat(fields.value[2].value),
		})).put(window.route("stakes.tradeout", { stake: props.stake.id }), {
			onSuccess() {
				stopPolling();
			},
		});
	};
</script>

<template>
	<AppLayout>
		<div class="sm:px-3.5">
			<div class="px-2 sm:px-0">
				<BreadCrumbs class="mt-3" :crumbs="crumbs" />
			</div>
			<div class="grid px-2 sm:px-0 pb-6 pt-3">
				<h1
					class="text-lg sm:text-3xl md:text-4xl text-gray-850 dark:text-white font-inter font-extrabold">
					{{ $t("Trade out") }}
				</h1>
				<div class="w-full">
					<StakeSidebarCard
						class="flex flex-col sm:flex-row sm:justify-between"
						:show-actions="false"
						:stake="stake" />
				</div>
				<div
					class="p-6 mt-4 rounded bg-white dark:bg-gray-800 transition-colors duration-300">
					<div class="flex justify-center space-x-4 mb-6">
						<PrimaryButton
							class="uppercase text-xs"
							@click="calculationType = 'backing'"
							:primary="calculationType == 'backing'"
							:secondary="calculationType != 'backing'"
							:disabled="!!props.stake">
							Backing First
						</PrimaryButton>
						<PrimaryButton
							class="uppercase text-xs"
							@click="calculationType = 'laying'"
							:secondary="calculationType != 'laying'"
							:disabled="!!props.stake">
							Laying First
						</PrimaryButton>
					</div>
					<div
						class="flex justify-between py-3 my-4 border rounded border-gray-250 dark:border-gray-650 items-center">
						<div class="px-3">
							<div>
								Available Markets
								<RefreshCw
									@click="reload"
									:class="{ 'animate-spin': reloading }"
									class="w-4 h-4 inline-flex cursor-pointer hover:rotate-90 transition-transform" />
							</div>
							<p>Select a market with enough Liquidity</p>
						</div>
						<MarketButtons
							@select="setOdds"
							:isLay="stake.type === 'lay'"
							:odds="odds" />
					</div>
					<div class="grid grid-cols-4 gap-4 mb-6">
						<div
							v-for="(field, index) in fields"
							:key="index"
							class="flex flex-col">
							<FormInput
								type="number"
								input-classes="[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
								step="0.1"
								:id="field.id"
								:label="field.label"
								:autofocus="field.autofocus ?? false"
								v-model="field.value"
								:disabled="field.disabled">
								<template v-if="field.trail" #trail>
									<CurrencySymbol
										class="uppercase text-xs font-bold font-inter"
										v-if="field.trail === 'money'" />
									<span
										class="uppercase text-xs font-bold font-inter"
										v-else>
										{{ field.trail }}
									</span>
								</template>
							</FormInput>
						</div>
					</div>

					<div class="mb-6">
						<label class="mb-2 block text-gray-800 dark:text-white">
							Partial Trade-Out ({{ partialTradeOut }}%)
							<MoneyFormat
								v-if="stake"
								class="ml-2"
								:amount="stake.filled" />
						</label>
						<input
							v-model="partialTradeOut"
							:disabled="!!props.stake"
							type="range"
							min="0"
							max="100"
							step="1"
							class="w-full" />
						<div
							class="flex justify-between text-gray-800 dark:text-white">
							<span>0%</span>
							<span>50%</span>
							<span>100%</span>
						</div>
					</div>

					<div
						v-for="(result, index) in results"
						:key="index"
						class="mb-4">
						<div class="flex justify-between items-center">
							<span
								:class="
									result.value > 0
										? 'text-emerald-700 dark:text-emerald-500'
										: 'text-red-700 dark:text-red-500'
								">
								{{ result.label }}
							</span>
							<span
								class="text-2xl text-gray-800 dark:text-white">
								<MoneyFormat :amount="result.value" />
							</span>
						</div>
					</div>
					<div
						v-if="['partial', 'matched'].includes('stake.status')"
						class="w-full flex justify-end">
						<PrimaryButton
							:disabled="form.processing"
							@click="tradeOut"
							primary>
							<Loading
								v-if="form.processing"
								class="!w-4 !h-4 mr-2 -ml-1" />
							Trade out your stake
						</PrimaryButton>
					</div>
					<div v-else class="w-full flex gap-2 justify-end">
						<PrimaryButton
							link
							:href="route('accounts.statement')"
							secondary>
							<Loading
								v-if="form.processing"
								class="!w-4 !h-4 mr-2 -ml-1" />
							View Your Stakes
						</PrimaryButton>
					</div>
				</div>
			</div>
		</div>
	</AppLayout>
</template>
