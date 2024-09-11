<script setup lang="ts">
	import { XIcon } from "lucide-vue-next";

	import OddsFormat from "../OddsFormat.vue";
	defineProps({
		bet: Object,
	});
	defineEmits(["remove"]);
</script>

<template>
	<div class="flex-1 px-2.5 py-2 group">
		<div class="flex items-center relative">
			<div class="flex flex-1 flex-col">
				<span
					class="flex-1 font-bold text-[13px] min-w-full max-w-0 overflow-hidden text-ellipsis mb-[3px]">
					<a
						class="bg-transparent text-gray-800 dark:text-white font-bold font-inter no-underline"
						href="/event/44024496/sport/football/england-professional-development-league/2024/09/03/13-00/barnsley-u21-vs-bristol-city-u21/">
						{{ bet.game }}
					</a>
				</span>
				<span
					class="text-[10px] font-bold uppercase tracking-[0.8px] min-w-full max-w-0 overflow-hidden text-ellipsis">
					<a
						class="bg-transparent text-gray-400 dark:text-gray-400 no-underline"
						href="/event/44024496/sport/football/england-professional-development-league/2024/09/03/13-00/barnsley-u21-vs-bristol-city-u21/?market=77684490">
						{{ bet.market }}
					</a>
				</span>
			</div>
			<button
				@click="$emit('remove')"
				class="w-[22px] text-gray-600 dark:text-gray-300 hover:text-red-500 dark:hover:text-red-400 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute top-1 right-1 bg-gray-100 hover:bg-red-50 dark:bg-gray-600 dark:hover:bg-red-400/20 h-[22px] rounded-full flex items-center justify-center self-start overflow-visible uppercase outline-none p-1 border-none"
				type="button">
				<XIcon class="w-3 h-3" />
			</button>
		</div>
		<div class="flex items-center text-xs font-bold justify-between">
			<div class="flex flex-1">
				<span
					class="min-w-full max-w-0 overflow-hidden text-ellipsis font-bol">
					<span class="text-emerald-600 dark:text-emerald-400">
						For
					</span>
					&nbsp;
					<span class="text-gray-800 dark:text-white">
						{{ bet.bet }}
					</span>
				</span>
			</div>
			<div class="flex flex-1 justify-end">
				<span
					class="min-w-full text-end max-w-0 overflow-hidden text-ellipsis font-bold">
					<span class="text-gray-400 dark:text-gray-400">ODDS</span>
					&nbsp;
					<OddsFormat
						:odds="bet.price"
						class="text-emerald-600 dark:text-emerald-400" />
				</span>
			</div>
		</div>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
