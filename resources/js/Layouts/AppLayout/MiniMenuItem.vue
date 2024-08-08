<script setup>
import {ref} from "vue";

import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {Link} from "@inertiajs/vue3";

defineProps({
	menu: Object,
});
const timeout = ref(null);
const buttonRef = ref(null);
const dropdownRef = ref(null);
const openMenu = () => buttonRef.value.$el.click();
const closeMenu = () =>
	dropdownRef.value?.$el?.dispatchEvent(
		new KeyboardEvent("keydown", {
			key: "Escape",
			bubbles: true,
			cancelable: true,
		}),
	);
const onMouseEnter = (closed) => {
	clearTimeout(timeout.value);
	closed && openMenu();
};
const onMouseLeave = (open) => {
	open && (timeout.value = setTimeout(() => closeMenu(), 200));
};
</script>

<template>
	<div
		class="py-3"
		v-if="menu.name === 'line'"
	>
		<hr class="border-gray-300 dark:border-gray-700" />
	</div>
	<div
		v-else-if="menu.url"
		class="h-10 items-center relative group gap-x-2 cursor-pointer flex font-semibold pl-6 pr-3 whitespace-nowrap w-full hover:bg-gray-100/50 dark:hover:bg-gray-900/50 mb-2"
	>
		<Link
			v-if="menu.route"
			:href="menu.route"
			class="text-2xl mr-3 focus-visible:outline-none group-hover:text-emerald-500 dark:group-hover:text-emerald-400"
			:class="
				menu.active
					? 'text-emerald-500 dark:text-emerald-400'
					: ' text-gray-900 dark:text-gray-200'
			"
		>
			<component
				class="w-7 h-7"
				:is="menu.icon"
			/>
		</Link>
		<a
			v-else
			:href="menu.url"
			target="_blank"
			:title="menu.name"
			class="text-2xl mr-3 focus-visible:outline-none text-emerald-500 dark:text-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-300"
		>
			<component
				class="w-7 h-7"
				:is="menu.icon"
			/>
		</a>
	</div>
	<Menu
		as="div"
		v-else
		v-slot="{open}"
		class="inline-block relative z-30"
	>
		<div
			@mouseover="onMouseEnter(!open)"
			@mouseleave="onMouseLeave(open)"
			class="h-10 z-10 items-center gap-x-2 cursor-pointer flex font-semibold pl-6 pr-3 whitespace-nowrap w-full hover:bg-gray-100/50 dark:hover:bg-gray-900/50 mb-2"
		>
			<MenuButton
				ref="buttonRef"
				class="text-2xl mr-3 focus-visible:outline-none"
			>
				<component
					:class="
						menu.active || open
							? 'text-emerald-500 dark:text-emerald-400'
							: ' text-gray-900 dark:text-gray-200'
					"
					class="w-7 h-7"
					:is="menu.icon"
				/>
			</MenuButton>
		</div>
		<transition
			enter-active-class="transition duration-100 ease-out"
			enter-from-class="transform scale-95 opacity-0"
			enter-to-class="transform scale-100 opacity-100"
			leave-active-class="transition duration-75 ease-in"
			leave-from-class="transform scale-100 opacity-100"
			leave-to-class="transform scale-95 opacity-0"
		>
			<MenuItems
				v-show="open"
				ref="dropdownRef"
				@mouseover="onMouseEnter()"
				@mouseleave="onMouseLeave(open)"
				static
				as="ul"
				class="left-full top-0 flex flex-col outline-none absolute border border-gray-200 dark:border-gray-700 z-30 bg-white dark:bg-gray-800"
			>
				<li
					class="text-xs border-b border-gray-200 dark:border-gray-700 uppercase bg-gray-50 dark:bg-gray-800 font-semibold dark:hover:bg-gray-800 min-w-[250px] px-6 py-1.5 z-30"
				>
					<span>{{ menu.name }}</span>
				</li>
				<MenuItem
					v-for="sub in menu.submenu"
					:key="sub.id"
				>
					<li
						:class="[
							sub.active
								? 'text-emerald-500 dark:text-emerald-400 bg-emerald-200/40 border-l-4 border-emerald-500'
								: ' text-gray-600 dark:text-gray-400',
						]"
						class="text-base font-semibold hover:text-emerald-500 dark:hover:text-emerald-400 bg-white dark:bg-gray-900 dark:hover:bg-gray-800 min-w-[250px] px-6 py-3 z-30"
					>
						<Link
							class="h-full w-full flex items-center"
							:href="sub.url"
							preserve-state
							><span>{{ sub.name }}</span></Link
						>
					</li>
				</MenuItem>
			</MenuItems>
		</transition>
	</Menu>
</template>
