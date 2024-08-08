<script setup>
	import { HiSolidCubeTransparent } from "oh-vue-icons/icons";

	import VueIcon from "@/Components/VueIcon.vue";
</script>
<template>
	<div class="h-96 flex items-center justify-center">
		<div class="p-8 w-full flex items-center justify-center">
			<div class="flex flex-col items-center">
				<VueIcon
					class="w-12 h-12 text-gray-400"
					:icon="HiSolidCubeTransparent" />
				<div class="text-gray-400 text-2xl font-semibold">
					<slot>{{ title }}</slot>
				</div>
				<p class="mt-5">
					You can try changing you
					<strong class="text-emerald-500">search</strong>
					or
					<strong class="text-sky-500">filter</strong>
					parameters
				</p>
			</div>
		</div>
	</div>
</template>
