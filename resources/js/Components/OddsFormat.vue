<script setup>
	import { computed } from "vue";

	import { usePage } from "@inertiajs/vue3";

	const props = defineProps({
		odds: Number, // decimal odds
	});

	const page = usePage();
	const oddsConfig = computed(
		() => page.props.auth?.user?.odds_type ?? "decimal",
	);

	const convertToAmericanOdds = () => {
		if (props.odds >= 2.0) {
			return `+${Math.round((props.odds - 1) * 100)}`;
		} else {
			return `-${Math.round(100 / (props.odds - 1))}`;
		}
	};

	const convertToPercentageOdds = () => {
		return `${((1 / props.odds) * 100).toFixed(2)}%`;
	};

	const displayValue = computed(() => {
		if (oddsConfig.value === "american_odds")
			return convertToAmericanOdds();
		if (oddsConfig.value === "percentage_odds")
			return convertToPercentageOdds();
		return props.odds.toFixed(2);
	});
</script>

<template>
	<slot :odds="displayValue">
		<span>{{ displayValue }}</span>
	</slot>
</template>
