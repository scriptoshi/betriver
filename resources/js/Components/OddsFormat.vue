<script setup>
	import { computed } from "vue";

	import { usePage } from "@inertiajs/vue3";

	const props = defineProps({
		odds: {
			type: [Number, String],
			required: true,
		},
	});

	const convertedOdds = computed(() => {
		const oddsType = usePage().props.auth.user.odds_type;
		if (oddsType === "american") {
			return convertToAmerican(props.odds);
		} else if (oddsType === "percentage") {
			return convertToPercentage(props.odds);
		}
		return props.odds;
	});

	function convertToAmerican(decimal) {
		if (decimal >= 2) {
			return `+${Math.round((decimal - 1) * 100)}`;
		} else {
			return `-${Math.round(100 / (decimal - 1))}`;
		}
	}

	function convertToPercentage(decimal) {
		return `${((1 / decimal) * 100).toFixed(2)}%`;
	}
</script>
<template>
	<slot :odds="convertedOdds">
		<span v-bind="$attrs">{{ convertedOdds }}</span>
	</slot>
</template>
