<script setup>
defineProps({
	active: {
		type: Boolean,
		default: false,
	},
});
</script>
<template>
	<button
		:class="
			active
				? 'border-emerald-600 text-white bg-emerald-600 hover:bg-emerald-600 dark:hover:bg-emerald-400'
				: 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-50 bg-transparent'
		"
		class="border rounded-[2px] min-w-[2.25rem] py-0.5 px-2 inline-block text-[10px] font-bold tracking-[0.5px] uppercase transition-colors duration-300 ease-in-out cursor-pointer text-center"
	>
		<slot></slot>
	</button>
</template>
