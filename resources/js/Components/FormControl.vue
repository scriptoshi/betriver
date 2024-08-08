<script setup>
defineProps({
	error: String,
});
</script>
<template>
	<div
		:class="
			!!error
				? 'bg-red-100/50 dark:bg-red-900/10  text-red-500 dark:text-white border-red-500 dark:border-red-400 focus-within:border-red-500 hover:border-red-500'
				: ' bg-white dark:bg-gray-900  text-gray-800 border-gray-300 dark:border-gray-600 focus-within:border-emerald-600 hover:border-emerald-600'
		"
		class="block w-full h-auto p-2 text-sm font-mediuml rounded-md bg-clip-padding border shadow-none transition-transform ease-in-out duration-150 disabled:text-gray-700 dark:disabled:text-gray-400 disabled:bg-gray-100 dark:disabled:bg-gray-600 group"
	>
		<slot />
	</div>
</template>
