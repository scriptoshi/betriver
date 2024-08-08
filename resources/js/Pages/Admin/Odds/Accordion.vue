<script setup>
	import { ref } from "vue";

	import { HiSolidChevronDown } from "oh-vue-icons/icons";

	import VueIcon from "@/Components/VueIcon.vue";

	const props = defineProps({
		initiallyOpen: {
			type: Boolean,
			default: false,
		},
	});

	const isOpen = ref(props.initiallyOpen);

	const toggleAccordion = () => {
		isOpen.value = !isOpen.value;
	};
</script>
<template>
	<div class="w-full bg-white dark:bg-gray-800 text-gray-700 dark:text-white">
		<button
			@click="toggleAccordion"
			type="button"
			class="w-full px-4 py-3 text-2xl flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
			<slot></slot>
			<VueIcon
				:class="{ 'transform rotate-180': isOpen }"
				class="w-5 h-5 transition-transform duration-200"
				:icon="HiSolidChevronDown" />
		</button>
		<div v-show="isOpen" class="px-4 py-3">
			<slot name="content"></slot>
		</div>
	</div>
</template>
