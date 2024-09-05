<script setup>
	defineProps({
		trade: Object,
	});
</script>

<template>
	<span
		class="rounded pointer-events-none relative cursor-[unset] text-left text-sm font-bold uppercase h-auto leading-5 whitespace-nowrap w-auto inline-block mt-[-3px] py-0">
		<span
			class="font-extrabold font-inter text-xs text-gray-700 dark:text-gray-200 whitespace-nowrap">
			{{ trade?.percentage_price ?? 0 }}%
		</span>
		<span
			class="font-extrabold font-inter bg-gray-200 text-gray-800 whitespace-nowrap inline-block text-[10px] h-[11px] leading-[11px] opacity-60 text-center min-w-[auto] relative ml-1 px-0.5 py-0 rounded-sm -top-px">
			{{ trade?.price ?? 0 }}
		</span>
	</span>
</template>
