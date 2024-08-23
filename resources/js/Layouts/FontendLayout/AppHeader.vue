<script setup>
	import { HiSearch, RiMenuFill } from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import DarkSwitch from "@/Layouts/FontendLayout/Dropdowns/DarkSwitch.vue";
	import HelpDropDown from "@/Layouts/FontendLayout/Dropdowns/HelpDropDown.vue";
	defineProps({
		leftSidebarOpen: Boolean,
		rightSidebarOpen: Boolean,
	});
	const emit = defineEmits(["toggleLeftSidebar", "toggleRightSidebar"]);
	const toggleLeftSidebar = () => emit("toggleLeftSidebar");
	const toggleRightSidebar = () => emit("toggleRightSidebar");
</script>
<template>
	<header
		class="fixed container-fluid w-full top-0 z-50 flex justify-start items-center bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 contain-size flex-nowrap h-[52px]">
		<div class="flex w-full items-center">
			<button
				@click="toggleLeftSidebar"
				class="px-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
				<VueIcon
					:icon="RiMenuFill"
					:open="open"
					@click="toggle"
					:class="
						leftSidebarOpen
							? ' text-gray-500 dark:text-gray-400 '
							: 'text-sky-600 dark:text-sky-400'
					"
					class="w-6 h-6" />
			</button>
			<div class="text-gray-800 ml-4 dark:text-white font-bold text-xl">
				Smarkets
			</div>
			<FormInput
				input-classes="!border-none !bg-gray-200 dark:!bg-gray-800 !py-1.5"
				class="w-56 lg:ml-[102px]"
				size="xs">
				<template #lead>
					<VueIcon :icon="HiSearch" class="text-gray-400 w-4 h-4" />
				</template>
			</FormInput>
		</div>
		<div
			class="flex items-center justify-end flex-nowrap h-full lg:flex-[2]">
			<HelpDropDown />
			<DarkSwitch />

			<button
				@click="toggleRightSidebar"
				class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
				<XIcon v-if="rightSidebarOpen" size="24" />
				<MenuIcon v-else size="24" />
			</button>
		</div>
	</header>
</template>
