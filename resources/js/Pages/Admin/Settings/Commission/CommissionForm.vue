<script setup>
	import { ref } from "vue";

	import { router, useForm } from "@inertiajs/vue3";
	import {
		HiRefresh,
		MdAddchartOutlined,
		MdCardgiftcard,
		MdHotelclassOutlined,
	} from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { ucfirst } from "@/hooks";

	const props = defineProps({
		settings: Object,
		level: String,
	});

	const form = useForm({
		name: props.settings?.name,
		description: props.settings?.description,
		limits: props.settings?.limits,
		max_daily_deposit: props.settings?.max_daily_deposit,
		max_monthly_deposit: props.settings?.max_monthly_deposit,
		exchange_max_bet: props.settings?.exchange_max_bet,
		exchange_min_bet: props.settings?.exchange_min_bet,
		bookie_max_bet: props.settings?.bookie_max_bet,
		bookie_min_bet: props.settings?.bookie_min_bet,
		commission_profit: props.settings?.commission_profit,
		commission_bet: props.settings?.commission_bet,
		deposit_fees: props.settings?.deposit_fees,
		withdraw_fees: props.settings?.withdraw_fees,
		optin_fees: props.settings?.optin_fees,
	});
	const save = () => {
		form.post(
			window.route("admin.settings.levels.store", { level: props.level }),
		);
	};
	const reloading = ref(false);
	const reload = () => {
		reloading.value = true;
		router.reload({ onFinish: () => (reloading.value = false) });
	};
	const icons = {
		level_one: MdCardgiftcard,
		level_two: MdAddchartOutlined,
		level_three: MdHotelclassOutlined,
	};
</script>
<template>
	<div class="card">
		<div class="card-body p-6 grid gap-4">
			<div class="flex justify-between items-center">
				<div class="flex items-center">
					<VueIcon
						:icon="icons[level]"
						v-if="icons[level]"
						class="mr-2 h-6 w-6 text-sky-600 dark:text-sky-400" />
					<h3 class="text-lg !text-sky-600 dark:!text-sky-400">
						{{ ucfirst(level.replace("_", " ")) }}
						config
					</h3>
				</div>
				<a @click.prevent="reload" v-tippy="$t('Reload')" href="#">
					<VueIcon
						:class="{ 'animate-spin': reloading }"
						:icon="HiRefresh" />
				</a>
			</div>
			<FormInput label="Level Name" v-model="form.name" />
			<FormInput label="Level Description" v-model="form.description" />
			<FormInput label="Limits Guide" v-model="form.limits" />
			<FormInput label="Optin (Join) Fees" v-model="form.optin_fees">
				<template #trail>
					{{ $page.props.currency.currency_code }}
				</template>
			</FormInput>
			<div class="grid sm:grid-cols-2 gap-3">
				<FormInput
					label="Max Daily Deposit"
					v-model="form.max_daily_deposit">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
				<FormInput
					label="Max Monthly Deposit"
					v-model="form.max_monthly_deposit">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
			</div>
			<div class="grid sm:grid-cols-2 gap-3">
				<FormInput
					label="Min Exchange Bet"
					v-model="form.exchange_min_bet">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
				<FormInput
					label="Max Exchange Bet"
					v-model="form.exchange_max_bet">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
			</div>
			<div class="grid sm:grid-cols-2 gap-3">
				<FormInput label="Min Bookie Bet" v-model="form.bookie_min_bet">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
				<FormInput label="Max Bookie Bet" v-model="form.bookie_max_bet">
					<template #trail>
						{{ $page.props.currency.currency_code }}
					</template>
				</FormInput>
			</div>
			<div class="grid sm:grid-cols-2 gap-3">
				<FormInput
					label="Commission on Bet"
					help="Profit or Loss "
					v-model="form.commission_bet">
					<template #trail>%</template>
				</FormInput>
				<FormInput
					label="Commission on Profit"
					help="Profit only."
					v-model="form.commission_profit">
					<template #trail>%</template>
				</FormInput>
			</div>
			<div class="grid sm:grid-cols-2 gap-3">
				<FormInput label="Deposit Fees" v-model="form.deposit_fees">
					<template #trail>%</template>
				</FormInput>
				<FormInput label="Withdraw Fees" v-model="form.withdraw_fees">
					<template #trail>%</template>
				</FormInput>
			</div>

			<div class="flex justify-end">
				<PrimaryButton @click="save" primary>
					<Loading
						class="mr-2 -ml-1"
						v-if="form.processing && saving == 1" />
					Update Settings
				</PrimaryButton>
			</div>
		</div>
	</div>
</template>
