<script setup>
	import { ref } from "vue";

	import { BiTicketDetailed, RiMenuFill } from "oh-vue-icons/icons";

	import ApplicationLogo from "@/Components/ApplicationLogo.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import DarkSwitch from "@/Layouts/FontendLayout/Dropdowns/DarkSwitch.vue";
	import FeedbackModal from "@/Layouts/FontendLayout/Dropdowns/FeedbackModal.vue";
	import HelpDropDown from "@/Layouts/FontendLayout/Dropdowns/HelpDropDown.vue";
	import LoginModal from "@/Layouts/FontendLayout/Dropdowns/LoginModal.vue";
	import UserDropDown from "@/Layouts/FontendLayout/Dropdowns/UserDropDown.vue";
	import SearchInput from "@/Layouts/FontendLayout/SearchInput.vue";
	defineProps({
		leftSidebarOpen: Boolean,
		rightSidebarOpen: Boolean,
	});
	const emit = defineEmits(["toggleLeftSidebar", "toggleRightSidebar"]);
	const toggleLeftSidebar = () => emit("toggleLeftSidebar");
	const toggleRightSidebar = () => emit("toggleRightSidebar");
	const showFeedback = ref(false);
	const showLoginModal = ref(false);

	const openLoginModal = () => {
		showLoginModal.value = true;
	};

	const closeLoginModal = () => {
		showLoginModal.value = false;
	};
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
					:class="
						leftSidebarOpen
							? ' text-gray-500 dark:text-gray-400 '
							: 'text-sky-600 dark:text-sky-400'
					"
					class="w-6 h-6" />
			</button>
			<div
				class="text-gray-800 flex items-center ml-4 dark:text-white font-bold text-xl">
				<ApplicationLogo
					v-if="$page.props.appLogo"
					class="w-5 h-5 mr-2" />
				<span v-else>{{ $page.props?.appName }}</span>
			</div>
			<SearchInput />
		</div>
		<div
			class="flex items-center justify-end flex-nowrap h-full lg:flex-[2]">
			<HelpDropDown class="hidden lg:flex" />
			<DarkSwitch />
			<button
				@click="toggleRightSidebar"
				class="h-full px-5 flex lg:hidden items-center hover:bg-gray-150 dark:hover:bg-gray-750 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
				<VueIcon
					:icon="BiTicketDetailed"
					class="w-6 h-6 rotate-[135deg]" />
			</button>
			<UserDropDown
				v-if="$page.props.auth?.user"
				@feedback-modal="showFeedback = true"
				class="-mr-2.5" />
			<template v-else>
				<button
					@click.prevent="openLoginModal"
					class="h-full px-5 font-inter whitespace-nowrap font-extrabold text-xs uppercase items-center hover:bg-gray-150 dark:hover:bg-gray-750 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
					Log in
				</button>
				<div class="flex pl-4">
					<PrimaryButton
						primary
						link
						href="/register"
						class="self-center py-1 font-inter font-extrabold whitespace-nowrap uppercase !text-xs">
						Create account
					</PrimaryButton>
				</div>
			</template>
			<FeedbackModal v-model:open="showFeedback" />
			<LoginModal :show="showLoginModal" @close="closeLoginModal" />
		</div>
	</header>
</template>
