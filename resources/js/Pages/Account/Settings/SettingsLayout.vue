<script setup>
	import { Head } from "@inertiajs/vue3";

	import FrontendLayout from "@/Layouts/FrontendLayout.vue";
	import MenuLink from "@/Pages/Account/Settings/MenuLink.vue";
	defineProps({
		personal: Object,
	});
</script>

<template>
	<Head title="Settings" />
	<FrontendLayout>
		<div class="px-3.5 mb-12">
			<div class="grid py-6">
				<h1
					class="text-3xl text-gray-750 dark:text-white font-inter font-semibold">
					{{ $t("Settings") }}
				</h1>
			</div>
			<div
				class="my-2 mx-2.5 flex items-center space-x-4 overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-track-transparent">
				<MenuLink
					:active="route().current('accounts.settings')"
					:href="route('accounts.settings')">
					{{ $t("General") }}
				</MenuLink>
				<MenuLink
					:active="route().current('accounts.limits')"
					:href="route('accounts.limits')">
					{{ $t("Limits") }}
				</MenuLink>
				<MenuLink
					:active="route().current('accounts.alerts')"
					:href="route('accounts.alerts')">
					{{ $t("Alerts") }}
				</MenuLink>
			</div>
			<slot></slot>
		</div>
	</FrontendLayout>
</template>
