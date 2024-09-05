<script setup>
	import { computed } from "vue";

	import { XMarkIcon } from "@heroicons/vue/24/outline";
	import { Link, usePage } from "@inertiajs/vue3";
	import { breakpointsTailwind, useBreakpoints } from "@vueuse/core";

	import SiteLogo from "@/Components/ApplicationLogo.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Lang from "@/Layouts/AppLayout/Lang.vue";
	import { useMenu } from "@/Layouts/AppLayout/Menu/useSoccer";
	import MenuItem from "@/Layouts/AppLayout/MenuItem.vue";
	import MiniMenuItem from "@/Layouts/AppLayout/MiniMenuItem.vue";

	const config = computed(() => usePage().props.config);

	const { fullMenu: menus, starrableMenus } = useMenu();
	const breakpoints = useBreakpoints(breakpointsTailwind);
	const isLg = breakpoints.greaterOrEqual("sm");
	defineProps({
		expanded: Boolean,
	});
	const emit = defineEmits(["draw"]);
	const draw = (val) => emit("draw", val);

	function currentRoute(route = [], params = {}) {
		if (!route) return false;
		if (typeof route === "string")
			return window.route().current(route, params);
		return route.filter((rt) => window.route().current(rt)).length > 0;
	}
</script>
<template>
	<div
		v-if="isLg"
		:class="{ 'min-w-[240px]': expanded }"
		class="min-h-screen sticky z-1 top-0 flex-auto flex-col bg-gray-200/50 dark:bg-gray-900 flex-shrink-0 transition-all duration-300 ease-in-out">
		<Link
			href="/"
			:class="expanded ? 'px-6' : 'pl-6'"
			class="py-4 w-auto flex items-center">
			<SiteLogo class="w-10 h-auto mr-3" />
			<h3 class="font-extralight" v-if="expanded">BETN</h3>
		</Link>
		<CollapseTransition dimension="width">
			<div
				v-show="expanded"
				class="h-[calc(100vh-4rem)] overflow-y-auto max-w-[242px] scrollbar-thin scrollbar-track-transparent dark:scrollbar-track-transparent scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-thumb-rounded-lg">
				<nav class="pb-8 mb-8">
					<div>
						<ul class="gap-y-2 grid">
							<li
								class="text-sky-500 text-xs font-semibold tracking-widest uppercase py-1.5 px-6"
								role="heading"
								aria-level="2">
								QUICKLINKS
							</li>
							<template
								v-for="menu in $page.props.quicklinks"
								:key="menu">
								<MenuItem
									v-if="starrableMenus[menu]"
									:menu="starrableMenus[menu]"
									:active="
										menu.route
											? currentRoute(menu.route)
											: false
									" />
							</template>

							<li
								class="text-sky-500 text-xs font-semibold tracking-widest uppercase py-1.5 px-6"
								role="heading"
								aria-level="2">
								CATEGORIES
							</li>
							<MenuItem
								v-for="menu in menus"
								:key="menu.id"
								:menu="menu"
								:active="
									menu.route
										? currentRoute(menu.route)
										: false
								" />
						</ul>
					</div>
				</nav>
			</div>
		</CollapseTransition>
		<nav v-show="!expanded" class="pb-8 relative">
			<div class="flex relative flex-col">
				<MiniMenuItem
					v-for="menu in menus"
					:key="menu.id"
					:menu="menu" />
			</div>
		</nav>
		<Lang :expanded="expanded" />
	</div>

	<!-- drawer component -->
	<template v-else>
		<div
			:class="expanded ? 'transform-none' : '-translate-x-full'"
			class="fixed z-40 h-screen overflow-y-auto bg-white w-80 dark:bg-gray-800 transition-transform left-0 top-0"
			tabindex="-1">
			<div
				class="flex items-center justify-between p-4 border-gray-300 dark:border-gray-600 border-b">
				<div class="flex items-center">
					<SiteLogo class="w-10 h-auto mr-3" />
					<h5
						class="text-base font-semibold text-gray-500 uppercase dark:text-gray-400">
						{{ config.appName }}
					</h5>
				</div>
				<button
					type="button"
					@click="draw(false)"
					class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
					<XMarkIcon class="w-5 h-5" />
				</button>
			</div>
			<div class="py-8">
				<ul class="space-y-2">
					<MenuItem
						v-for="menu in menus"
						:key="menu.id"
						:menu="menu" />
				</ul>
			</div>
			<Lang :expanded="expanded" />
		</div>
		<div
			v-if="expanded"
			class="bg-gray-900/70 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-30"></div>
	</template>
</template>
