<script setup>
	import { computed } from "vue";

	import MoneyFormat from "../MoneyFormat.vue";

	const props = defineProps({
		blank: Boolean,
		ask: String,
		price: [Number, String],
		amount: [Number, String],
		active: Boolean,
		lay: Boolean,
	});
	defineEmits(["go"]);
	const classes = computed(() => {
		if (props.ask || props.active)
			return props.lay
				? "bg-emerald-600 hover:bg-emerald-500 dark:hover:bg-emerald-400 dark:bg-emerald-500"
				: "bg-sky-600 hover:bg-sky-500 dark:hover:bg-sky-400 dark:bg-sky-500";
		return props.lay
			? "bg-gray-150 !text-gray-600 dark:!text-white hover:!text-white hover:bg-emerald-500 dark:hover:bg-emerald-500 dark:bg-gray-750"
			: "bg-gray-150 !text-gray-600 dark:!text-white hover:!text-white hover:bg-sky-500 dark:hover:bg-sky-500 dark:bg-gray-750";
	});
</script>
<template>
	<span
		v-if="blank"
		class="h-full cursor-default inline-flex flex-[1_0_52px] mx-1 mb-2 flex-col min-w-0 lining-nums">
		<span
			:class="classes"
			disabled
			class="text-sm pointer-events-none font-bold uppercase flex relative text-center whitespace-nowrap transition-colors duration-300 text-white h-8 items-center justify-center lining-nums rounded-sm">
			&nbsp;
		</span>
		<span
			class="block relative text-center whitespace-nowrap text-[0.6875rem] leading-[0.8rem] lining-nums mt-[5px] rounded-sm">
			&nbsp;
		</span>
	</span>
	<span
		v-else-if="ask"
		@click="$emit('go', ask, lay)"
		class="h-full cursor-pointer inline-flex flex-[1_0_52px] mx-1 mb-2 flex-col min-w-0 lining-nums">
		<span
			:class="classes"
			class="font-bold whitespace-nowrap lining-nums text-sm uppercase flex relative text-center transition-colors duration-300 text-white h-8 items-center justify-center rounded-sm">
			{{ ask }}
		</span>
		<span
			class="font-bold whitespace-nowrap lining-nums block relative text-[0.6875rem] leading-[0.8rem] mt-[5px] rounded-sm">
			&nbsp;
		</span>
	</span>
	<span
		v-else
		@click="$emit('go', price, lay)"
		class="h-full cursor-pointer inline-flex flex-[1_0_52px] mx-1 mb-2 flex-col min-w-0 lining-nums">
		<span
			:class="classes"
			class="font-bold whitespace-nowrap lining-nums text-sm uppercase flex relative text-center transition-colors duration-300 text-white h-8 items-center justify-center rounded-sm">
			{{ price }}
		</span>
		<span
			class="font-bold whitespace-nowrap lining-nums block relative text-[0.6875rem] leading-[0.8rem] mt-[5px] rounded-sm">
			<MoneyFormat v-if="amount" :amount="amount" />
		</span>
	</span>
</template>
