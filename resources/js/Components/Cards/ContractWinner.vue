<template>
	<li>
		<div
			class="border-t border-gray-250 dark:border-gray-750 flex items-center flex-nowrap min-[70px]">
			<div class="flex-[1_1_12rem] min-w-0">
				<div class="pb-[0.3rem]">
					<span class="lining-nums">
						<span class="lining-nums">Real Salt Lake</span>
					</span>
				</div>
				<div class="flex items-center"></div>
			</div>
			<span
				class="rounded pointer-events-none relative flex-[0_0_4rem] text-right cursor-[unset] text-sm font-bold uppercase lining-nums">
				<span
					class="font-inter font-bold whitespace-nowrap lining-nums">
					99.99%
				</span>
				<span
					class="font-inter font-bold whitespace-nowrap lining-nums inline-block text-[10px] h-[11px] leading-[11px] opacity-60 text-center min-w-[1.5rem] px-0.5 py-0 rounded-sm">
					1.0001
				</span>
			</span>
			<span
				class="text-sm font-bold text-center uppercase flex-[10_0_7.5rem] max-w-[360px] lining-nums px-4 py-0">
				Winner
			</span>
		</div>
	</li>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
