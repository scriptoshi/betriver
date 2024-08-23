<script setup>
	import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
	import { Link as InertiaLink } from "@inertiajs/vue3";
	import { MdHelpoutlineRound } from "oh-vue-icons/icons";
	import { useI18n } from "vue-i18n";

	import VueIcon from "@/Components/VueIcon.vue";

	const { t } = useI18n();
	const userNavigation = [
		{
			name: t("Help centre"),
			href: "#",
		},

		{
			name: t("Getting Started"),
			href: "#",
		},

		{
			name: t("Bet Calculator"),
			href: "#",
		},
		{
			name: t("Welcome Bonus Explained"),
			href: "#",
		},
		{
			name: t("Bet Types"),
			href: "#",
		},
		{
			name: t("What is Lay Betting?"),
			href: "#",
		},
		{
			name: t("Betting Strategies"),
			href: "#",
		},
	];
</script>
<template>
	<Menu v-slot="{ open }" as="div" class="relative dropdown h-full">
		<MenuButton
			:class="
				open
					? 'bg-gray-150 dark:bg-gray-750 text-emerald-500'
					: 'bg-transparent text-gray-800 dark:text-gray-100'
			"
			class="h-full px-5 hover:bg-gray-100 dark:hover:bg-gray-800">
			<VueIcon class="w-7 h-7" :icon="MdHelpoutlineRound" />
		</MenuButton>
		<transition
			enter-active-class="transition ease-out duration-200"
			enter-from-class="transform opacity-0 scale-95"
			enter-to-class="transform opacity-100 scale-100"
			leave-active-class="transition ease-in duration-75"
			leave-from-class="transform opacity-100 scale-100"
			leave-to-class="transform opacity-0 scale-95">
			<MenuItems
				class="aside bottom top-[50px] place-left tracking-wider pb-3 text-xs mt-1 absolute right-0 w-72 rounded-sm with-shadow pt-4 bg-white dark:bg-gray-750 focus:outline-none">
				<h3
					class="px-6 pb-3 border-b text-gray-800 dark:text-white dark:border-gray-650">
					Help Resources
				</h3>
				<div class="pt-3">
					<MenuItem
						v-for="item in userNavigation"
						:key="item.name"
						v-slot="{ active }">
						<InertiaLink
							:href="item.href"
							:class="[
								active
									? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-100'
									: 'text-gray-700 dark:text-gray-200 ',
								'group flex items-center px-6 py-2.5 text-base hover:bg-emerald-gray-300 dark:hover:bg-gray-700',
							]">
							{{ item.name }}
						</InertiaLink>
					</MenuItem>
				</div>
			</MenuItems>
		</transition>
	</Menu>
</template>
<style>
	.with-shadow {
		box-shadow: 0 15px 18px 0 rgba(0, 0, 0, 0.5);
	}
</style>
