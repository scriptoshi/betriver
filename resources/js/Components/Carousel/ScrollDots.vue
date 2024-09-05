<script setup>
	import { useCarousel } from "../ui/carousel";

	const { selectedIndex, scrollSnaps, scrollTo } = useCarousel();
</script>
<template>
	<div
		class="flex items-center justify-center mt-2 mb-2 [grid-column:span_3] w-full">
		<i
			v-for="(_, index) in scrollSnaps"
			:key="index"
			@click.stop="scrollTo(index)"
			:class="
				index === selectedIndex
					? 'bg-emerald-500'
					: 'bg-gray-900 dark:bg-white'
			"
			class="inline-block h-2 w-2 rounded-full my-0 mx-2 transition-colors duration-300 cursor-pointer box-border shrink-0 flex-wrap m-0"></i>
	</div>
</template>
