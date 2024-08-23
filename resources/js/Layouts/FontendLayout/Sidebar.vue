<script setup>
	import MenuItem from "@/Layouts/FontendLayout/Sidebar/MenuItem.vue";
	import { useMenu } from "@/Layouts/FontendLayout/useMenu.js";

	const { fullMenu: menus, starrableMenus } = useMenu();

	defineProps({
		expanded: Boolean,
	});

	function currentRoute(route = [], params = {}) {
		if (!route) return false;
		if (typeof route === "string")
			return window.route().current(route, params);
		return route.filter((rt) => window.route().current(rt)).length > 0;
	}
</script>
<template>
	<div>
		<ul class="gap-y-2 grid">
			<li
				class="text-sky-500 text-xs font-semibold tracking-widest uppercase py-1.5 px-6"
				role="heading"
				aria-level="2">
				QUICKLINKS
			</li>
			<template v-for="menu in $page.props.quicklinks" :key="menu">
				<MenuItem
					v-if="starrableMenus[menu]"
					:menu="starrableMenus[menu]"
					:active="menu.route ? currentRoute(menu.route) : false" />
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
				:active="menu.route ? currentRoute(menu.route) : false" />
		</ul>
	</div>
</template>
