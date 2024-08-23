<script setup>
	defineProps({
		count: Number,
		active: Boolean,
	});
</script>
<template>
	<span
		v-if="count > 0"
		class="flex items-center font-semibold flex-nowrap rounded-sm text-xs px-px py-[1px] transition-colors duration-200 before:content-[attr(count)] before:inline-block before:px-0.5 before:text-center before:min-w-[1.125rem] before:align-middle"
		:count="count ?? 0"></span>
</template>
