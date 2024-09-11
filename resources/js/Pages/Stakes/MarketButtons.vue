<script setup>
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import OddsFormat from "@/Components/OddsFormat.vue";
	defineProps({
		isLay: Boolean,
		odds: Array,
	});
	defineEmits(["select"]);
</script>
<template>
	<div class="flex max-w-xs w-full flex-wrap mt-0.5 m-0 px-2 py-0">
		<template v-for="(odd, index) in odds" :key="index">
			<span v-if="isLay" class="cursor-pointer w-1/3 lining-nums m-0">
				<span
					@click="$emit('select', odd.price * 1)"
					:class="
						index == 0
							? 'bg-emerald-600 first:bg-emerald-500 hover:bg-emerald-500 dark:hover:bg-emerald-400'
							: 'bg-gray-600 first:bg-gray-650 hover:bg-emerald-500 dark:hover:bg-emerald-400'
					"
					class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] text-white mr-0.5 m-0 px-0 py-1 rounded-sm">
					<OddsFormat :odds="odd.price * 1" #default="{ odds }">
						{{ odds }}
					</OddsFormat>
				</span>
				<span
					class="font-bold whitespace-nowrap lining-nums block relative text-center text-[0.6875rem] leading-[0.8rem] mr-0.5 mt-[5px] m-0 rounded-sm">
					<MoneyFormat
						billion
						:amount="odd.amount * 1"
						#default="{ amount }">
						{{ amount }}
					</MoneyFormat>
				</span>
			</span>
			<span v-else class="cursor-pointer w-1/3 lining-nums m-0">
				<span
					@click="$emit('select', odd.price * 1)"
					:class="
						index == 0
							? 'bg-sky-600 first:bg-sky-500 hover:bg-sky-500 dark:hover:bg-sky-400'
							: 'bg-gray-600 first:bg-gray-650 hover:bg-sky-500 dark:hover:bg-sky-400'
					"
					class="font-bold whitespace-nowrap lining-nums text-xs uppercase block relative text-center transition-[background-color] duration-[0.3s] text-white ml-0.5 m-0 px-0 py-1 rounded-sm">
					<OddsFormat :odds="odd.price * 1" #default="{ odds }">
						{{ odds }}
					</OddsFormat>
				</span>
				<span
					class="font-bold whitespace-nowrap lining-nums block relative text-center text-[0.6875rem] leading-[0.8rem] ml-0.5 mt-[5px] m-0 rounded-sm">
					<MoneyFormat
						billion
						:amount="odd.amount * 1"
						#default="{ amount }">
						{{ amount }}
					</MoneyFormat>
				</span>
			</span>
		</template>
	</div>
</template>
