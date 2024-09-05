<script setup>
	import MoneyFormat from "@/Components/MoneyFormat.vue";

	defineProps({
		stat: Object,
		isMoney: Boolean,
	});
</script>
<template>
	<div
		class="flex flex-[1_0_200px] flex-col text-center bg-white dark:bg-gray-850 rounded-sm mt-2.5 py-4 px-5">
		<div
			class="text-sm text-gray-550 dark:text-gray-250 font-bold tracking-[2px] text-left uppercase">
			{{ stat.info }}
		</div>
		<div
			class="flex-[1_0_auto] text-[2.5rem] font-bold font-inter py-6 text-gray-900 dark:text-white">
			<MoneyFormat v-if="isMoney" :amount="stat.stat" />
			<span v-else>{{ stat.stat }}</span>
		</div>
		<div
			v-if="stat.last_month != undefined"
			class="border-t text-[10px] text-gray-400 border-gray-150 dark:border-gray-700 font-inter font-bold tracking-[0.5px] pt-4 uppercase transition-colors">
			Last month:
			<MoneyFormat v-if="isMoney" :amount="stat.last_month" />
			<span v-else>{{ stat.last_month }}</span>
		</div>
	</div>
</template>
