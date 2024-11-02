<script setup>
	import { computed, ref } from "vue";

	import { usePage } from "@inertiajs/vue3";

	const props = defineProps({
		modelValue: {
			type: Number,
			required: true,
		},
	});

	const emit = defineEmits(["update:modelValue"]);

	const oddsType = computed(() => usePage().props.auth?.user?.odds_type);
	const isEditing = ref(false);
	const inputValue = ref("");

	const displayValue = computed(() => {
		if (isEditing.value) {
			return inputValue.value;
		}

		if (oddsType.value === "american") {
			return decimalToAmerican(props.modelValue);
		} else if (oddsType.value === "percentage") {
			return decimalToPercentage(props.modelValue);
		}
		return parseFloat(props.modelValue ?? 0).toFixed(2);
	});

	function handleInput(event) {
		inputValue.value = event.target.value.replace(/[^\d.-]/g, "");
	}

	function handleBlur() {
		isEditing.value = false;
		let decimalValue;

		if (oddsType.value === "american") {
			decimalValue = americanToDecimal(parseFloat(inputValue.value));
		} else if (oddsType.value === "percentage") {
			decimalValue = percentageToDecimal(parseFloat(inputValue.value));
		} else {
			decimalValue = parseFloat(inputValue.value);
		}

		if (!isNaN(decimalValue) && decimalValue > 0) {
			emit("update:modelValue", parseFloat(decimalValue.toFixed(2)));
		} else {
			inputValue.value = displayValue.value; // Reset to previous valid value
		}
	}

	function handleFocus() {
		isEditing.value = true;
		inputValue.value = displayValue.value;
	}

	function decimalToAmerican(decimal) {
		if (decimal >= 2) {
			return `+${Math.round((decimal - 1) * 100)}`;
		} else {
			return `-${Math.round(100 / (decimal - 1))}`;
		}
	}

	function americanToDecimal(american) {
		if (american > 0) {
			return 1 + american / 100;
		} else {
			return 1 - 100 / american;
		}
	}

	function decimalToPercentage(decimal) {
		return `${((1 / decimal) * 100).toFixed(2)}%`;
	}

	function percentageToDecimal(percentage) {
		return 100 / percentage;
	}
</script>
<template>
	<input
		:value="displayValue"
		@input="handleInput"
		@blur="handleBlur"
		@focus="handleFocus"
		v-bind="$attrs" />
</template>
