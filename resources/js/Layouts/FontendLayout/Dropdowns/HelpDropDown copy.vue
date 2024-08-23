<script setup>
	import { computed } from "vue";

	import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
	import {
		CogIcon,
		GiftIcon,
		Squares2X2Icon,
		UserPlusIcon as UserAddIcon,
		UserIcon,
	} from "@heroicons/vue/24/outline";
	import { Link as InertiaLink, usePage } from "@inertiajs/vue3";
	import { MdHelpoutlineRound } from "oh-vue-icons/icons";
	import { useI18n } from "vue-i18n";

	import VueIcon from "@/Components/VueIcon.vue";

	const user = computed(() => usePage().props.auth.user);
	const avatar = computed(
		() =>
			user.value?.avatar?.src ??
			user.value?.profile_photo_url ??
			user.value?.gravatar,
	);
	const { t } = useI18n();
	const userNavigation = [
		{
			name: t("Your Profile"),
			href: window.route("profile.edit", user.value.username),
			icon: UserIcon,
			active: window.route().current("profile.edit"),
		},

		{
			name: t("Linked Accounts"),
			href: "#",
			icon: CogIcon,
			active: false,
		},

		{
			name: t("Referral Earnings"),
			href: "#",
			icon: UserAddIcon,
			active: false,
		},
		{
			name: t("Your Farms"),
			href: "#",
			icon: Squares2X2Icon,
			active: false,
		},
		{
			name: t("Documentation"),
			href: "#",
			icon: GiftIcon,
			active: false,
		},
	];
</script>
<template>
	<Menu v-slot="{ open }" as="div" class="relative dropdown h-full">
		<MenuButton
			:class="
				open
					? 'bg-gray-100 dark:bg-gray-800 text-emerald-500'
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
				class="aside bottom place-left tracking-wider pb-3 text-xs mt-1 origin-top-right absolute right-0 w-72 rounded-sm with-shadow pt-4 bg-white dark:bg-gray-750 focus:outline-none">
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
							<component
								:is="item.icon"
								class="mr-3 h-5 w-5 text-gray-700 dark:text-gray-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
								aria-hidden="true" />
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
