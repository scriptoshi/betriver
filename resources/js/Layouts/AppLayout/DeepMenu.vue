<script setup>
	import { onMounted, ref } from "vue";

	import { Link } from "@inertiajs/vue3";
	import { HiChevronRight } from "oh-vue-icons/icons";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Favourite from "@/Components/Favourite.vue";
	import GameCount from "@/Components/GameCount.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		menu: Object,
		active: Boolean,
	});
	const isOpen = ref(false);
	onMounted(() => (isOpen.value = props.active));
</script>
<template>
	<div
		:class="
			isOpen
				? 'bg-gray-300 dark:bg-gray-700/70'
				: 'bg-gray-200 dark:bg-gray-800'
		"
		class="items-center text-sm gap-x-2 cursor-pointer font-semibold">
		<div
			@click="isOpen = !isOpen"
			:class="
				isOpen
					? 'bg-gray-300 dark:bg-gray-900/70 text-gray-900 dark:text-white'
					: ' text-gray-600 hover:text-gray-700 dark:hover:text-white dark:text-gray-300'
			"
			class="items-center h-8 text-sm gap-x-2 cursor-pointer flex font-semibold pl-10 pr-6 whitespace-nowrap w-full hover:bg-emerald-200/50 dark:hover:bg-emerald-700/30 transition-colors">
			<div class="h-full w-full flex items-center justify-between">
				<div>{{ menu.name }}</div>
				<div class="flex items-center">
					<GameCount
						class="border border-gray-300 group-hover:border-emerald-400 bg-white dark:border-none dark:bg-gray-700 text-xs group-hover:bg-emerald-600 group-hover:text-white"
						:count="menu.count" />
					<VueIcon
						:icon="HiChevronRight"
						:class="isOpen ? 'rotate-90' : ''"
						class="w-4 h-4 transition-transform duration-300" />
				</div>
			</div>
		</div>
		<CollapseTransition>
			<ul v-show="isOpen" class="max-w-[242px]">
				<Link
					v-for="sub in menu.deepMenu"
					:key="sub.id"
					:class="
						route().current(sub.route, sub.params)
							? 'bg-gray-300 text-gray-900 dark:text-white'
							: ' text-gray-600 hover:text-gray-700 dark:hover:text-white dark:text-gray-300'
					"
					class="min-h-10 group py-1 h-full w-full flex flex-nowrap font-semibold pl-14 pr-6 whitespace-nowrap items-center justify-between hover:bg-emerald-200/50 dark:hover:bg-emerald-700/30 transition-colors"
					:href="route(sub.route, sub.params)">
					<span
						class="flex-1 whitespace-normal line-clamp-2 text-sm overflow-hidden">
						{{ sub.name.replace(" - ", " ") }}
					</span>
					<div class="flex items-center">
						<Favourite class="z-5" :favId="sub.key" />
						<GameCount
							class="border border-gray-300 group-hover:border-emerald-400 bg-white dark:border-none dark:bg-gray-800 text-xs group-hover:bg-emerald-600 group-hover:text-white"
							:count="sub.count" />
					</div>
				</Link>
			</ul>
		</CollapseTransition>
	</div>
</template>
