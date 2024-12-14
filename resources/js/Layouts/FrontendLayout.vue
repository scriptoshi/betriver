<script setup>
	import { ref, watch } from "vue";

	import { Link } from "@inertiajs/vue3";
	import { useDark } from "@vueuse/core";
	import { ChevronDown } from "lucide-vue-next";

	import EventCard from "@/Components/Cards/EventCard.vue";
	import StakeSidebarCard from "@/Components/Cards/StakeSidebarCard.vue";
	import TicketSidebarCard from "@/Components/Cards/TicketSidebarCard.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import AlertMessages from "@/Layouts/AlertMessages.vue";
	import AppHeader from "@/Layouts/FontendLayout/AppHeader.vue";
	import Sidebar from "@/Layouts/FontendLayout/Sidebar.vue";
	if (!localStorage.getItem("vueuse-color-scheme"))
		localStorage.setItem("vueuse-color-scheme", "dark");
	const isDarkMode = useDark();
	const leftSidebarOpen = ref(true);
	const rightSidebarOpen = ref(true);

	const toggleLeftSidebar = () => {
		leftSidebarOpen.value = !leftSidebarOpen.value;
	};

	const toggleRightSidebar = () => {
		rightSidebarOpen.value = !rightSidebarOpen.value;
	};
	const showBets = ref(false);
	const showTickets = ref(false);
	watch(showBets, (showBets) => {
		if (showBets) showTickets.value = false;
	});
	watch(showTickets, (showTickets) => {
		if (showTickets) showBets.value = false;
	});
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
					class="lg:z-0 z-40 w-[340px] 2xl:w-[440px] max-w-[820px] contain-strict p-0 right-0 overflow-y-auto fixed top-[52px] bottom-0 text-left transition duration-600 scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent dark:bg-gray-850">
					<div class="bg-white dark:bg-gray-800">
						<slot name="right-sidebar-top" />
						<template
							v-if="
								($page.props.auth.user?.stakes ?? []).length > 0
							">
							<div
								class="bg-gray-300 text-gray-900 text-xs dark:text-white dark:bg-gray-750 border-b border-gray-50 dark:border-gray-850 flex items-center justify-between px-2.5 uppercase font-inter tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
								<div
									@click="showBets = !showBets"
									class="flex-grow cursor-pointer">
									{{ $t("Your Bets") }}
								</div>

								<div class="flex items-center">
									<Link
										:href="
											route('accounts.statement', {
												types: 'bet',
											})
										"
										v-show="showBets"
										class="text-sky-600 text-xs dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-500 hover:underline">
										{{ $t("See All") }}
									</Link>
									<a
										class="h-full px-3"
										href="#"
										@click.prevent="showBets = !showBets">
										<ChevronDown
											:class="{
												'rotate-180': showBets,
											}"
											class="w-4 h-4 ml-2 transition-transform duration-300" />
									</a>
								</div>
							</div>
							<CollapseTransition>
								<div v-show="showBets">
									<StakeSidebarCard
										v-for="stake in $page.props.auth.user
											?.stakes ?? []"
										:key="stake.id"
										:stake="stake" />
								</div>
							</CollapseTransition>
						</template>
						<template
							v-if="
								($page.props.auth.user?.tickets ?? []).length >
								0
							">
							<div
								class="bg-gray-300 text-gray-900 dark:text-white dark:bg-gray-750 border-b border-gray-50 dark:border-gray-850 flex items-center justify-between px-2.5 uppercase font-inter text-xs tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
								<div
									@click="showTickets = !showTickets"
									class="flex-grow cursor-pointer">
									{{ $t("Your Tickets") }}
								</div>

								<div class="flex items-center">
									<Link
										:href="
											route('accounts.statement', {
												types: 'ticket',
											})
										"
										v-show="showTickets"
										class="text-sky-600 text-xs dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-500 hover:underline">
										{{ $t("See All") }}
									</Link>
									<a
										class="h-full px-3"
										href="#"
										@click.prevent="
											showTickets = !showTickets
										">
										<ChevronDown
											:class="{
												'rotate-180': showTickets,
											}"
											class="w-4 h-4 ml-2 transition-transform duration-300" />
									</a>
								</div>
							</div>
							<CollapseTransition>
								<div v-show="showTickets">
									<TicketSidebarCard
										v-for="ticket in $page.props.auth.user
											?.tickets ?? []"
										:key="ticket.id"
										:ticket="ticket" />
								</div>
							</CollapseTransition>
						</template>
						<slot name="right-sidebar">
							<div
								class="bg-gray-300 text-gray-900 dark:text-white dark:bg-gray-750 border-b border-gray-250 dark:border-gray-850 flex items-center px-2.5 uppercase font-inter text-sm tracking-[1px] font-bold h-12 box-border flex-shrink-0 flex-wrap m-0">
								Top Events
							</div>
							<div class="grid">
								<EventCard
									v-for="game in $page.props.popular"
									:key="game.slug"
									:game="game" />
							</div>
						</slot>
					</div>
				</aside>
			</div>
		</div>
	</div>
</template>
