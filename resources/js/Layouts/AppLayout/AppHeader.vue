<script setup>
	import { computed } from "vue";

	import { usePage } from "@inertiajs/vue3";
	import {
		CoAlignLeft,
		HiSearch,
		HiSolidGift,
		LaUserPlusSolid,
		RiAwardLine,
		RiUserLine,
	} from "oh-vue-icons/icons";
	import { useI18n } from "vue-i18n";

	import FormInput from "@/Components/FormInput.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import DarkSwitch from "@/Layouts/AdminLayout/DarkSwitch.vue";

	const user = computed(() => usePage().props.user);
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
			href: window.route("profile.edit", user.value?.username),
			icon: RiUserLine,
			active: window.route().current("profile.edit"),
		},

		{
			name: t("Referral Earnings"),
			href: "#", // window.route("user.referrals"),
			icon: LaUserPlusSolid,
			active: false,
		},
		{
			name: t("Your Farms"),
			href: "#",
			icon: RiAwardLine,
			active: false,
		},
		{
			name: t("Documentation"),
			href: "https://docs.betn.io",
			icon: HiSolidGift,
			active: false,
		},
	];

	defineProps({
		open: Boolean,
	});
	const emit = defineEmits(["toggle"]);
	const toggle = () => emit("toggle");
</script>
<template>
	<header
		class="bg-gray-50 dark:bg-gray-800 flex sticky top-0 w-full z-20 border-b border-gray-200 dark:border-gray-700">
		<div
			class="h-14 item items-center flex justify-between py-0 px-4 relative w-full">
			<div class="flex items-center justify-start">
				<div class="rouded-full cursor-pointer mx-1 p-2">
					<div class="text-2xl flex items-center">
						<VueIcon
							:icon="CoAlignLeft"
							:open="open"
							@click="toggle"
							class="w-6 h-6 text-gray-500 dark:text-gray-400" />
					</div>
				</div>
				<FormInput
					input-classes="!border-none !bg-gray-200 dark:!bg-gray-700 !py-1.5"
					class="w-56"
					size="xs">
					<template #lead>
						<VueIcon :icon="HiSearch" class="text-gray-400" />
					</template>
				</FormInput>
			</div>
			<div class="flex flex-row items-center space-x-3">
				<DarkSwitch class="ml-2" />
			</div>
		</div>
	</header>
</template>
