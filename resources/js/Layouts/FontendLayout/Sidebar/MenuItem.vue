<script setup>
	import { onMounted, ref } from "vue";

	import { Link } from "@inertiajs/vue3";
	import { BiArrowUpRight, HiChevronRight } from "oh-vue-icons/icons";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Favourite from "@/Components/Favourite.vue";
	import GameCount from "@/Components/GameCount.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import DeepMenu from "@/Layouts/FontendLayout/Sidebar/DeepMenu.vue";
	const props = defineProps({
		menu: Object,
		active: Boolean,
	});
	const isOpen = ref(false);
	onMounted(() => (isOpen.value = !!props.active));
	function currentRoute(route = [], params = {}) {
		if (!route) return false;
		if (typeof route === "string")
			return window.route().current(route, params);
		return route.filter((rt) => window.route().current(rt)).length > 0;
	}
</script>
<template>
	<div>
		<div class="py-3" v-if="menu.type === 'line'">
			<hr class="border-gray-300 dark:border-gray-700" />
		</div>
		<li
			v-else-if="menu.type == 'title'"
			class="text-sky-500 text-sm font-semibold tracking-widest uppercase py-1.5 px-4"
			role="heading"
			aria-level="2">
			{{ menu.name }}
		</li>
		<template v-else>
			<a
				v-if="menu.type === 'external'"
				:href="menu.url"
				target="_blank"
				:class="
					menu.active
						? 'text-emerald-500 dark:text-emerald-400'
						: ' text-gray-900 dark:text-gray-200'
				"
				class="items-center text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none mb-2 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-200/50 dark:hover:bg-emerald-700/50 transition-colors">
				<span class="flex items-center">
					<component
						class="w-5 h-5 mr-2 opacity-80 text-emerald-600 dark:text-emerald-400"
						:is="menu.icon" />
					<div class="whitespace-nowrap">{{ menu.name }}</div>
				</span>
				<VueIcon :icon="BiArrowUpRight" />
			</a>
			<Link
				v-else-if="menu.type === 'link'"
				:href="route(menu.route, menu.params)"
				:class="
					menu.active
						? 'text-emerald-500 dark:text-emerald-400'
						: ' text-gray-900 dark:text-gray-200'
				"
				class="items-center text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none mb-2 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-200/50 dark:hover:bg-emerald-700/50 transition-colors">
				<span class="flex items-center">
					<component
						class="w-5 h-5 mr-2 opacity-80 text-emerald-600 dark:text-emerald-400"
						:is="menu.icon" />
					<div
						class="overflow-hidden line-clamp-1 text-ellipsis max-w-[105px]">
						{{ menu.name }}
					</div>
				</span>
				<span class="text-lg flex items-center space-x-1 mt-1">
					<Favourite v-if="menu.star" class="z-5" :favId="menu.key" />
					<GameCount
						class="border border-gray-300 group-hover:border-emerald-400 bg-white dark:border-none dark:bg-gray-700 text-xs group-hover:bg-emerald-600 group-hover:text-white"
						:count="menu.count" />
				</span>
			</Link>
			<div
				v-else
				@click="isOpen = !isOpen"
				:class="[
					menu.active
						? 'text-emerald-500 dark:text-emerald-400'
						: ' text-gray-900 dark:text-gray-200',
					{ 'bg-gray-300 dark:bg-gray-750': isOpen },
				]"
				class="items-center group text-sm cursor-pointer flex font-semibold h-8 justify-between px-6 select-none hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-200/50 dark:hover:bg-emerald-700/50">
				<span class="flex items-center">
					<component
						class="w-5 h-5 mr-2 opacity-80 text-emerald-600 dark:text-emerald-400"
						:is="menu.icon" />
					<div
						class="overflow-hidden line-clamp-1 text-ellipsis max-w-[100px]">
						{{ menu.name }}
					</div>
				</span>
				<span class="text-lg flex items-center space-x-1 mt-1">
					<Favourite v-if="menu.star" class="z-5" :favId="menu.key" />
					<GameCount
						class="border border-gray-300 group-hover:border-emerald-400 bg-white dark:border-none dark:bg-gray-700 text-xs group-hover:bg-emerald-600 group-hover:text-white"
						:count="menu.count" />
					<VueIcon
						v-if="menu.submenu"
						:icon="HiChevronRight"
						:class="isOpen ? 'rotate-90' : ''"
						class="w-4 h-4 transition-transform duration-300" />
				</span>
			</div>
			<CollapseTransition>
				<ul v-show="isOpen" class="bg-gray-200 dark:bg-gray-800">
					<template v-for="sub in menu.submenu" :key="sub.id">
						<DeepMenu
							v-if="sub.deepMenu"
							:active="
								$page.props.sport && $page.props.country
									? sub?.params?.sport ===
											$page.props.sport &&
									  sub?.params?.country ===
											$page.props.country
									: false
							"
							:menu="sub"
							:country="
								$page.props.country + '-' + sub?.params?.country
							"
							:sport="
								sub?.params?.sport + '-' + $page.props.sport
							" />
						<div
							v-else
							:class="
								route().current(sub.route, sub.params)
									? 'bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white'
									: ' text-gray-600 hover:text-gray-700 dark:hover:text-white dark:text-gray-300'
							"
							class="items-center h-8 text-sm gap-x-2 cursor-pointer flex font-semibold pl-10 pr-6 whitespace-nowrap w-full hover:bg-emerald-200/50 dark:hover:bg-emerald-700/30 transition-colors">
							<Link
								class="h-full w-full group flex items-center justify-between"
								:href="route(sub.route, sub.params)">
								<div
									class="overflow-hidden text-ellipsis max-w-[105px]">
									{{ sub.name }}
								</div>
								<div class="flex items-center">
									<Favourite
										v-if="sub.star"
										class="z-5 group-hover:text-emerald-500"
										:favId="sub.key" />
									<GameCount
										class="border border-gray-300 group-hover:border-emerald-400 bg-white dark:border-none dark:bg-gray-700 text-xs group-hover:bg-emerald-600 group-hover:text-white"
										:count="sub.count" />
								</div>
							</Link>
						</div>
					</template>
				</ul>
			</CollapseTransition>
		</template>
	</div>
</template>
