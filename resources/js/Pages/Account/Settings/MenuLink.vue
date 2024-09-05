<script setup>
	import { Link } from "@inertiajs/vue3";

	defineProps({
		active: Boolean,
		btn: Boolean,
	});
</script>
<template>
	<button
		v-if="btn"
		:class="
			active
				? 'text-gray-900 dark:text-white border-b-2 border-emerald-500 dark:border-emerald-400'
				: 'text-gray-600 dark:text-gray-400 border-emerald-500/20'
		"
		class="font-bold font-inter hover:border-b-2 inline-block text-[10px] tracking-[0.5px] py-1.5 px-4 uppercase transition-colors duration-300">
		<slot />
	</button>
	<Link
		v-else
		:class="
			active
				? 'text-gray-900 dark:text-white border-b-2 border-emerald-500 dark:border-emerald-400'
				: 'text-gray-600 dark:text-gray-400 border-emerald-500/20'
		"
		class="font-bold font-inter hover:border-b-2 inline-block text-[10px] tracking-[0.5px] py-1.5 px-4 uppercase transition-colors duration-300">
		<slot />
	</Link>
</template>
