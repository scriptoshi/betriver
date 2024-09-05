<script setup>
	defineProps({
		lay: Number,
		back: Number,
		titles: Boolean,
	});
</script>
<template>
	<div
		class="text-gray-650 dark:text-gray-350 border-t border-gray-150 dark:border-gray-700 flex uppercase text-[0.7rem] font-bold flex-nowrap py-2">
		<span class="flex-[1_1_12rem] min-w-0 lining-nums">
			<span class="text-[10px] font-inter font-bold" v-if="titles">
				{{ $t("Contract") }}
			</span>
		</span>
		<span
			class="rounded pointer-events-none [direction:rtl] relative flex-[0_0_4rem] text-right inline-block whitespace-nowrap w-[46px] lining-nums">
			<span class="text-[10px] font-inter font-bold" v-if="titles">
				{{ $t("Last Traded Price") }}
			</span>
		</span>
		<span
			title="Lay Overround"
			class="block cursor-default flex-[5_0_3.75rem] font-inter font-bold flex-row-reverse h-auto max-w-[180px] overflow-hidden self-center text-center lining-nums">
			{{ lay }}%
		</span>
		<span
			title="Back Overround"
			class="block cursor-default flex-[5_0_3.75rem] font-inter font-bold flex-row-reverse h-auto max-w-[180px] overflow-hidden self-center text-center lining-nums">
			{{ back }}%
		</span>
	</div>
</template>
