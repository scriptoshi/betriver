<script setup>
	import { computed } from "vue";

	const emit = defineEmits(["update:checked"]);

	const props = defineProps({
		checked: {
			type: [Array, Boolean],
			required: true,
		},
		value: {
			default: null,
		},
	});

	const proxyChecked = computed({
		get() {
			return props.checked;
		},

		set(val) {
			emit("update:checked", val);
		},
	});
</script>

<template>
	<input
		type="checkbox"
		:value="value"
		v-model="proxyChecked"
		class="form-checkbox size-5 rounded border-gray-400/70 checked:bg-gray-500 checked:border-gray-500 hover:border-gray-500 focus:border-gray-500 dark:border-navy-400 dark:checked:bg-navy-400 basic" />
</template>
