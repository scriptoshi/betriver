<script setup>
	import { buttonVariants } from "@/Components/ui/button";
	import { cn } from "@/lib/utils";
	import { ChevronRight } from "lucide-vue-next";
	import { RangeCalendarNext, useForwardProps } from "radix-vue";
	import { computed } from "vue";

	const props = defineProps({
		step: { type: String, required: false },
		nextPage: { type: Function, required: false },
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
	<RangeCalendarNext
		:class="
			cn(
				buttonVariants({ variant: 'outline' }),
				'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
				props.class,
			)
		"
		v-bind="forwardedProps">
		<slot>
			<ChevronRight class="h-4 w-4" />
		</slot>
	</RangeCalendarNext>
</template>
