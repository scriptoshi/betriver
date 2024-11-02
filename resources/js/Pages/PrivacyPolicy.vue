<script setup>
	import { Head } from "@inertiajs/vue3";
	import VueMarkdown from "vue-markdown-render";

	import Layout from "@/Layouts/FrontendLayout.vue";
	defineProps({
		policy: String,
	});
</script>

<template>
	<Head title="Privacy Policy" />
	<Layout>
		<main class="mx-auto container px-4">
			<div class="bg-white dark:bg-gray-800 p-8 mt-5 rounded-md">
				<div class="mark">
					<VueMarkdown :source="policy" />
				</div>
			</div>
		</main>
	</Layout>
</template>
<style>
	.mark li {
		@apply text-gray-500 dark:text-gray-400;
	}
	.mark p {
		@apply text-gray-700 dark:text-gray-300;
	}
	.mark h2 {
		@apply text-gray-800 dark:text-gray-100 font-semibold my-6 text-xl;
	}
	.mark h1 {
		@apply text-xl md:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4;
	}
</style>
