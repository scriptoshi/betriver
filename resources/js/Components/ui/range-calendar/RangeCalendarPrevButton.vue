<script setup>
	import { buttonVariants } from "@/Components/ui/button";
	import { cn } from "@/lib/utils";
	import { ChevronLeft } from "lucide-vue-next";
	import { RangeCalendarPrev, useForwardProps } from "radix-vue";
	import { computed } from "vue";

	const props = defineProps({
		step: { type: String, required: false },
		prevPage: { type: Function, required: false },
		asChild: { type: Boolean, required: false },
		as: { type: null, required: false },
		class: { type: null, required: false },
	});

	const delegatedProps = computed(() => {
		const { class: _, ...delegated } = props;

		return delegated;
	});

	const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
	<RangeCalendarPrev
		:class="
			cn(
				buttonVariants({ variant: 'outline' }),
				'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
				props.class,
			)
		"
		v-bind="forwardedProps">
		<slot>
			<ChevronLeft class="h-4 w-4" />
		</slot>
	</RangeCalendarPrev>
</template>
