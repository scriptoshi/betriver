<script setup>
	import { Link } from "@inertiajs/vue3";

	defineProps({
		link: Boolean,
		button: Boolean,
	});
</script>
<template>
	<button
		v-if="button"
		class="border dark:border-gray-600 inline-flex cursor-pointer items-center justify-center p-4 text-center tracking-wide outline-none transition-all duration-200 focus:outline-none disabled:pointer-events-none disabled:opacity-60 h-6 w-6 rounded-full hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25">
		<slot />
	</button>
	<Link
		v-else-if="link"
		class="border dark:border-gray-600 inline-flex cursor-pointer items-center justify-center p-4 text-center tracking-wide outline-none transition-all duration-200 focus:outline-none disabled:pointer-events-none disabled:opacity-60 h-6 w-6 rounded-full hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25">
		<slot />
	</Link>
	<a
		v-else
		class="border dark:border-gray-600 inline-flex cursor-pointer items-center justify-center p-4 text-center tracking-wide outline-none transition-all duration-200 focus:outline-none disabled:pointer-events-none disabled:opacity-60 h-6 w-6 rounded-full hover:bg-gray-300/20 focus:bg-gray-300/20 active:bg-gray-300/25 dark:hover:bg-gray-300/20 dark:focus:bg-gray-300/20 dark:active:bg-gray-300/25">
		<slot />
	</a>
</template>
