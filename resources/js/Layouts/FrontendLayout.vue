<script setup>
	import { ref } from "vue";

	import { useDark } from "@vueuse/core";

	import AlertMessages from "@/Layouts/AlertMessages.vue";
	import AppHeader from "@/Layouts/FontendLayout/AppHeader.vue";
	import Sidebar from "@/Layouts/FontendLayout/Sidebar.vue";
	const isDarkMode = useDark();
	const leftSidebarOpen = ref(true);
	const rightSidebarOpen = ref(true);

	const toggleLeftSidebar = () => {
		leftSidebarOpen.value = !leftSidebarOpen.value;
	};

	const toggleRightSidebar = () => {
		rightSidebarOpen.value = !rightSidebarOpen.value;
	};
</script>
<template>
	<div
		class="flex h-full bg-white dark:bg-gray-900 flex-1 flex-col flex-nowrap max-w-full scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
		<AlertMessages />
		<div class="flex-auto flex flex-col flex-nowrap">
			<AppHeader
				@toggleRightSidebar="toggleRightSidebar"
				@toggleLeftSidebar="toggleLeftSidebar"
				:leftSidebarOpen="leftSidebarOpen"
				:rightSidebarOpen="rightSidebarOpen" />
			<div class="pt-[52px] -mt-px bg-white dark:bg-gray-900 flex flex-1">
				<nav
					:class="[
						{
							'lg:w-0 transition duration-600': !leftSidebarOpen,
						},
						leftSidebarOpen ? 'translate-x-0' : '-translate-x-full',
					]"
					:style="{
						visibility: leftSidebarOpen
							? 'lg:visible'
							: 'lg:hidden',
					}"
					class="contain-strict border-r border-gray-250 dark:border-gray-650 left-0 w-[240px] z-40 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent overflow-y-auto fixed top-[52px] bottom-0 text-left transition duration-600">
					<Sidebar class="py-4 bg-gray-100 dark:bg-gray-850" />
				</nav>
				<div
					id="main"
					:class="{
						'lg:ml-[240px]': leftSidebarOpen,
						'lg:mr-[340px]': rightSidebarOpen,
					}"
					class="flex flex-col scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent flex-nowrap flex-[1_0] relative max-w-full transition-spacing duration-600">
					<main class="flex-[1_0_auto] bg-gray-150 dark:bg-gray-900">
						<slot />
					</main>
				</div>
				<aside
					:class="[
						{ 'lg:w-0': !rightSidebarOpen },
						rightSidebarOpen ? 'translate-x-0' : 'translate-x-full',
					]"
					:style="{
						visibility: rightSidebarOpen
							? 'lg:visible'
							: 'lg:hidden',
						scrollbarWidth: 'thin',
						scrollbarColor: isDarkMode
							? '#52525b transparent'
							: '#d4d4d8 transparent',
					}"
					class="lg:z-0 z-40 w-[340px] 2xl:w-[440px] contain-strict p-0 right-0 overflow-y-auto fixed top-[52px] bottom-0 text-left transition duration-600 scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">
					<div class="bg-white dark:bg-gray-800">
						<slot name="right-sidebar" />
					</div>
				</aside>
			</div>
		</div>
	</div>
</template>
