<script setup>
	import { ArrowLeft } from "lucide-vue-next";

	import { Button } from "@/Components/ui/button";
	import { cn } from "@/lib/utils";
	import { useCarousel } from "./useCarousel";

	const props = defineProps({
		class: { type: null, required: false },
	});

	const { orientation, canScrollPrev, scrollPrev } = useCarousel();
</script>

<template>
	<Button
		:disabled="!canScrollPrev"
		:class="
			cn(
				'touch-manipulation absolute h-8 w-8  z-10 rounded-full p-0',
				orientation === 'horizontal'
					? 'left-3 top-1/2 -translate-y-1/2'
					: '-top-12 left-1/2 -translate-x-1/2 rotate-90',
				props.class,
			)
		"
		variant="outline"
		@click="scrollPrev">
		<slot>
			<ArrowLeft class="h-4 w-4 text-current" />
			<span class="sr-only">Previous Slide</span>
		</slot>
	</Button>
</template>
