<script setup>
	import { ref } from "vue";

	import { useDark } from "@vueuse/core";

	import AppHeader from "@/Layouts/FontendLayout/AppHeader.vue";

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
		class="flex bg-white dark:bg-gray-900 text-white flex-1 flex-col flex-nowrap max-w-full scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
		<div class="flex-auto flex flex-col flex-nowrap">
			<AppHeader
				@toggleRightSidebar="toggleRightSidebar"
				@toggleLeftSidebar="toggleLeftSidebar"
				:leftSidebarOpen="leftSidebarOpen"
				:rightSidebarOpen="rightSidebarOpen" />
			<div
				class="pt-[52px] -mt-px bg-white dark:bg-gray-900 flex text-white flex-1">
				<nav
					:class="[
						{ 'lg:w-0 transition duration-600': !leftSidebarOpen },
						leftSidebarOpen ? 'translate-x-0' : '-translate-x-full',
					]"
					:style="{
						visibility: leftSidebarOpen
							? 'lg:visible'
							: 'lg:hidden',
					}"
					class="contain-strict left-0 w-[240px] z-40 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent lg:overflow-y-auto fixed top-[52px] bottom-0 text-left transition duration-600">
					<ul class="space-y-2 p-4 bg-white dark:bg-gray-800">
						<li class="text-gray-700 dark:text-gray-300">
							Menu Item 1
						</li>
						<li class="text-gray-700 dark:text-gray-300">
							Menu Item 2
						</li>
						<li class="text-gray-700 dark:text-gray-300">
							Menu Item 3
						</li>
						<li
							v-for="i in 50"
							:key="i"
							class="mt-4 text-gray-700 dark:text-gray-300">
							Menu Item{{ i }}
						</li>
					</ul>
				</nav>
				<div
					id="main"
					:class="{
						'lg:ml-[240px]': leftSidebarOpen,
						'lg:mr-[340px]': rightSidebarOpen,
					}"
					class="flex flex-col scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent flex-nowrap flex-1 relative max-w-full transition-spacing duration-600">
					<h1
						class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
						Main Content
					</h1>
					<p class="text-gray-700 dark:text-gray-300">
						This is where the main content would go. You can scroll
						this area independently.
					</p>
					<!-- Add more content here to demonstrate scrolling -->
					<p
						v-for="i in 20"
						:key="i"
						class="mt-4 text-gray-700 dark:text-gray-300">
						Scrollable content line {{ i }}
					</p>
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
					class="lg:z-0 z-40 w-[340px] 2xl:w-[440px] contain-strict p-0 right-0 lg:overflow-y-auto fixed top-[52px] bottom-0 text-left transition duration-600 scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">
					<div class="p-4 bg-white dark:bg-gray-800">
						<h2
							class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
							Right Sidebar
						</h2>
						<ul class="space-y-2">
							<li class="text-gray-700 dark:text-gray-300">
								Sidebar Item 1
							</li>
							<li class="text-gray-700 dark:text-gray-300">
								Sidebar Item 2
							</li>
							<li class="text-gray-700 dark:text-gray-300">
								Sidebar Item 3
							</li>
							<li
								v-for="i in 50"
								:key="i"
								class="mt-4 text-gray-700 dark:text-gray-300">
								Menu Item{{ i }}
							</li>
						</ul>
					</div>
				</aside>
			</div>
		</div>
	</div>
</template>
