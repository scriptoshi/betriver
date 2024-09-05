<script setup>
	import { computed } from "vue";

	import { usePage } from "@inertiajs/vue3";

	const props = defineProps({
		amount: {
			type: [Number, String],
			required: true,
			default: 0,
		},
		symbol: String,
		billion: Boolean,
	});

	const page = usePage();
	const config = computed(() => page.props.currency);
	const useBillions = (billion) => {
		let newValue = parseInt(billion) ?? 0;
		if (!newValue) return 0;
		if (newValue < 1000) return newValue;
		const suffixes = ["", "K", "M", "B", "T"];
		let suffixNum = 0;
		while (newValue >= 1000) {
			newValue /= 1000;
			suffixNum++;
		}
		newValue =
			newValue.toString().length > 2
				? newValue.toPrecision(3)
				: newValue.toPrecision();
		newValue += suffixes[suffixNum];
		return newValue;
	};
	const formattedAmount = computed(() => {
		if (isNaN(parseFloat(props.amount))) return 0;
		if (props.billion) return useBillions(parseFloat(props.amount ?? 0));
		return new Intl.NumberFormat(undefined, {
			style: "decimal",
			minimumFractionDigits: 2,
			maximumFractionDigits: 2,
		}).format(parseFloat(props.amount ?? 0));
	});

	const currencyDisplay = computed(() => {
		switch (config.value.currency_display) {
			case "symbol":
				return config.value.currency_symbol;
			case "code":
				return config.value.currency_code;
			case "auto":
			default:
				return (
					config.value.currency_symbol || config.value.currency_code
				);
		}
	});

	const displayValue = computed(() => {
		if (props.symbol) return `${formattedAmount.value} ${props.symbol}`;
		if (config.value.currency_display === "code") {
			return `${formattedAmount.value} ${currencyDisplay.value}`;
		} else {
			return `${currencyDisplay.value}${formattedAmount.value}`;
		}
	});
</script>
<template>
	<slot :amount="displayValue">
		<span v-bind="$attrs">{{ displayValue }}</span>
	</slot>
</template>
;
