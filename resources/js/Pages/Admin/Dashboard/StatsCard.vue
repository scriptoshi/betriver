<!-- StatsCard.vue -->

<script setup>
	import { computed } from "vue";

	import { Link } from "@inertiajs/vue3";
	import { HiSolidArrowSmRight } from "oh-vue-icons/icons";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		icon: {
			type: String,
			required: true,
		},
		iconBackgroundColor: {
			type: String,
			default: "bg-emerald-500",
		},
		value: {
			type: [Number, String],
			required: true,
		},
		label: {
			type: String,
			required: true,
		},
		change: {
			type: Number,
			required: true,
		},
		link: {
			type: String,
			required: true,
		},
		linkText: {
			type: String,
			required: true,
		},
		isMoney: Boolean,
	});
	const changePrefix = computed(() => (props.change > 0 ? "↑ " : "↓ "));
	const changeColor = computed(() =>
		props.change > 0 ? "text-green-500" : "text-red-500",
	);
</script>

<template>
	<div
		class="bg-white shadow-sm text-center dark:bg-gray-800 rounded-lg border dark:border-gray-600">
		<div class="p-6">
			<div class="flex items-center justify-center mb-4">
				<div class="p-3 rounded-md" :class="iconBackgroundColor">
					<component :is="icon" class="w-6 h-6 text-white" />
				</div>
			</div>
			<h3
				class="text-3xl font-inter font-extrabold text-gray-700 dark:text-gray-200">
				<MoneyFormat v-if="isMoney" :amount="value" />
				<span v-else>{{ value }}</span>
			</h3>
			<p class="text-gray-500 dark:text-gray-400">{{ label }}</p>
			<div class="flex font-inter items-center justify-center mt-4">
				<span
					:class="changeColor"
					class="text-sm px-3 py-2 border dark:border-gray-700 rounded-3xl font-semibold">
					{{ changePrefix }}{{ change }}%
				</span>
			</div>
		</div>
		<div
			class="w-full flex items-center group justify-center py-3 rounded-b-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900">
			<Link
				:href="link"
				class="text-emerald-500 transition-colors duration-300 font-bold group-hover:text-emerald-600 dark:text-emerald-400 dark:group-hover:text-emerald-300 inline-block">
				{{ linkText }}
			</Link>
			<VueIcon
				class="w-5 h-5 text-emerald-500 transition-colors duration-300 group-hover:text-emerald-600 dark:text-emerald-400 dark:group-hover:text-emerald-300"
				:icon="HiSolidArrowSmRight" />
		</div>
	</div>
</template>
