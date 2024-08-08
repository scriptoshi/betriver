<script setup>
	import { reactive, ref } from "vue";

	import { Link, usePage } from "@inertiajs/vue3";
	import {
		BiUiChecksGrid,
		CoBaseball,
		CoBasketball,
		FaCog,
		HiSolidChevronRight,
		MdSportsfootballOutlined,
		MdSportsvolleyballOutlined,
		MdWalletRound,
		RiAlarmWarningFill,
		RiStackFill,
		RiUserSettingsFill,
	} from "oh-vue-icons/icons";
	import { uid } from "uid";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import TopMenu from "@/Layouts/AdminLayout/TopMenu.vue";
	import AlertMessages from "@/Layouts/AlertMessages.vue";

	const menus = reactive([
		{
			text: "Dashboard",
			url: window.route("admin.dashboard"),
			active: window.route().current("admin.dashboard"),
			value: "dashboard",
			icon: BiUiChecksGrid,
			id: uid(),
		},
		{
			text: "Settings",
			value: "settings",
			url: window.route("admin.settings.index"),
			active:
				window.route().current("admin.settings.*") ||
				window.route().current("admin.commissions.*"),
			icon: FaCog,
			id: uid(),
			submenu: [
				{
					text: "Site Settings",
					url: window.route("admin.settings.site"),
					active: window.route().current("admin.settings.site"),
					value: "settings.site",
					id: uid(),
				},
				{
					text: "Notifications",
					url: window.route("admin.settings.notifications"),
					active: window
						.route()
						.current("admin.settings.notifications"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Payment Gateways",
					url: window.route("admin.settings.payments"),
					active: window.route().current("admin.settings.payments"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Seo & site meta",
					url: window.route("admin.settings.meta"),
					active: window.route().current("admin.settings.meta"),
					value: "meta",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Social Login",
					url: window.route("admin.settings.social"),
					active: window.route().current("admin.settings.social"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},

				{
					text: "Privacy & Policy",
					url: window.route("admin.settings.privacy"),
					active: window.route().current("admin.settings.privacy"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},

				{
					text: "Referral Payouts",
					url: window.route("admin.commissions.index"),
					active: window.route().current("admin.commissions.*"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
			],
		},
		{
			text: "Members",
			url: "#",
			active: window.route().current("admin.users.*"),
			value: "users",
			icon: RiUserSettingsFill,
			id: uid(),
			submenu: [
				{
					text: "All Members",
					url: window.route("admin.users.index"),
					active: window
						.route()
						.current("admin.users.index", { filter: null }),
					value: "users.index",
					id: uid(),
				},
				{
					text: "Account Banned",
					url: window.route("admin.users.index", {
						filter: "banned",
					}),
					active: window
						.route()
						.current("admin.users.index", { filter: "banned" }),
					value: "users.index",
					id: uid(),
				},
				{
					text: "Unverifed Email",
					url: window.route("admin.users.index", { filter: "email" }),
					active: window
						.route()
						.current("admin.users.index", { filter: "email" }),
					value: "users.index",
					id: uid(),
				},
				{
					text: "Unverifed Phone",
					url: window.route("admin.users.index", { filter: "phone" }),
					active: window
						.route()
						.current("admin.users.index", { filter: "phone" }),
					value: "users.index",
					id: uid(),
				},
				{
					text: "Unverifed KYC",
					url: window.route("admin.users.index", { filter: "kyc" }),
					active: window
						.route()
						.current("admin.users.index", { filter: "kyc" }),
					value: "users.index",
					id: uid(),
				},
				{
					text: "Has Balance",
					url: window.route("admin.users.index", {
						filter: "balance",
					}),
					active: window
						.route()
						.current("admin.users.index", { filter: "balance" }),
					value: "users.index",
					id: uid(),
				},
			],
		},
		{
			text: "Bets",
			url: "#",
			active:
				window.route().current("admin.slips.*") ||
				window.route().current("admin.tickets.*"),
			value: "frontend",
			icon: RiAlarmWarningFill,
			id: uid(),
			submenu: [
				{
					text: "Bet Exchange Slips",
					url: window.route("admin.slips.index"),
					active: window.route().current("admin.slips.index"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Exchange Trades",
					url: window.route("admin.trades.index"),
					active: window.route().current("admin.trades.index"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Bookie Tickets",
					url: window.route("admin.tickets.index"),
					active: window.route().current("admin.tickets.index"),
					value: "tickets",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
			],
		},
		{
			text: "Wallets",
			url: "#",
			active:
				window.route().current("admin.transactions.*") ||
				window.route().current("admin.trades.*"),
			value: "frontend",
			icon: MdWalletRound,
			id: uid(),
			submenu: [
				{
					text: "Deposits",
					url: window.route("admin.transactions.index"),
					active: window.route().current("admin.transactions.index"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Withdrawals",
					url: window.route("admin.transactions.index"),
					active: window.route().current("admin.transactions.index"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				{
					text: "Ref Payouts",
					url: window.route("admin.transactions.index"),
					active: window.route().current("admin.transactions.index"),
					value: "slips",
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
			],
		},

		{
			text: "Leagues",
			url: "#",
			active: window.route().current("admin.leagues.*"),
			value: "leagues",
			icon: CoBasketball,
			id: uid(),
			submenu: [
				{
					text: "All Leagues",
					url: window.route("admin.leagues.index", "all"),
					active: window
						.route()
						.current("admin.leagues.index", "all"),
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				...usePage().props.sports.map((sport) => {
					return {
						text: sport,
						url: window.route(
							"admin.leagues.index",
							sport.toLowerCase(),
						),
						active:
							window
								.route()
								.current(
									"admin.leagues.index",
									sport.toLowerCase(),
								) ||
							window
								.route()
								.current(
									"admin.leagues.create",
									sport.toLowerCase(),
								),
						icon: MdSportsvolleyballOutlined,
						id: uid(),
					};
				}),
			],
		},
		{
			text: "Teams",
			url: "#",
			active: window.route().current("admin.teams.*"),
			value: "packages",
			icon: MdSportsfootballOutlined,
			id: uid(),
			submenu: [
				{
					text: "All Teams",
					url: window.route("admin.teams.index", "all"),
					active: window.route().current("admin.teams.index", "all"),
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				...usePage().props.sports.map((sport) => {
					return {
						text: sport,
						url: window.route(
							"admin.teams.index",
							sport.toLowerCase(),
						),
						active:
							window
								.route()
								.current(
									"admin.teams.index",
									sport.toLowerCase(),
								) ||
							window
								.route()
								.current(
									"admin.teams.create",
									sport.toLowerCase(),
								),
						icon: MdSportsvolleyballOutlined,
						id: uid(),
					};
				}),
			],
		},
		{
			text: "Games",
			url: "#",
			active:
				window.route().current("admin.games.*") ||
				window.route().current("admin.odds.*") ||
				window.route().current("admin.scores.*"),
			value: "games",
			icon: RiStackFill,
			id: uid(),
			submenu: [
				{
					text: "All Games",
					url: window.route("admin.games.index", "all"),
					active: window.route().current("admin.games.index", "all"),
					icon: MdSportsvolleyballOutlined,
					id: uid(),
				},
				...usePage().props.sports.map((sport) => {
					return {
						text: sport,
						url: window.route(
							"admin.games.index",
							sport.toLowerCase(),
						),
						active:
							window
								.route()
								.current(
									"admin.games.index",
									sport.toLowerCase(),
								) ||
							window
								.route()
								.current(
									"admin.games.create",
									sport.toLowerCase(),
								),
						icon: MdSportsvolleyballOutlined,
						id: uid(),
					};
				}),
			],
		},
		{
			text: "Markets",
			url: window.route("admin.markets.index"),
			active: window.route().current("admin.markets.index"),
			value: "bets",
			icon: CoBaseball,
			id: uid(),
			submenu: [
				{
					text: "All Markets",
					url: window.route("admin.markets.index", "all"),
					active: window
						.route()
						.current("admin.markets.index", "all"),
					id: uid(),
				},
				...usePage().props.sports.map((sport) => {
					return {
						text: sport,
						url: window.route(
							"admin.markets.index",
							sport.toLowerCase(),
						),
						active:
							window
								.route()
								.current(
									"admin.markets.index",
									sport.toLowerCase(),
								) ||
							window
								.route()
								.current(
									"admin.markets.create",
									sport.toLowerCase(),
								),
						id: uid(),
					};
				}),
			],
		},

		/*,
		{
			text: "Chains",
			value: "chains",
			url: window.route("admin.amms.index"),
			active:
				window.route().current("admin.amms.index") ||
				window.route().current("admin.amms.create") ||
				window.route().current("admin.amms.edit"),
			icon: RiExchangeFill,
			id: uid(),
		} */
	]);

	const showSidebar = ref(false);
</script>
<template>
	<body class="bg-gray-50 h-screen">
		<AlertMessages />
		<TopMenu v-model="showSidebar" />
		<div
			class="flex overflow-hidden bg-white dark:bg-gray-800 h-full pt-14">
			<aside
				:class="showSidebar ? 'flex' : 'hidden'"
				class="fixed z-20 h-full top-0 left-0 pt-14 lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75">
				<div
					class="relative flex-1 pl-3 flex flex-col min-h-0 border-r border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pt-0">
					<div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
						<div class="flex-1 dark:divide-gray-600 space-y-1">
							<ul class="space-y-2 pb-16 pr-3">
								<li v-for="menu in menus" :key="menu.id">
									<template v-if="menu.submenu">
										<a
											href="#"
											@click="menu.active = !menu.active"
											:class="
												menu.active
													? 'text-emerald-600 bg-gray-100 dark:bg-gray-900 rounded-t-sm dark:text-emerald-400'
													: 'text-gray-900 dark:text-gray-300'
											"
											class="text-base font-normal flex items-center px-3 py-2 hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-gray-900 group">
											<VueIcon
												:class="
													menu.active
														? 'text-emerald-500  group-hover:text-emerald-700 dark:group-hover:text-emerald-300'
														: 'text-sky-500  group-hover:text-emerald-900 dark:group-hover:text-gray-100'
												"
												class="w-6 h-6 flex-shrink-0 transition duration-75 group-hover:text-emerald-700"
												:icon="menu.icon" />
											<span class="ml-3">
												{{ menu.text }}
											</span>
											<VueIcon
												class="ml-auto w-5 h-5 transition-transform duration-300"
												:class="{
													' rotate-90': menu.active,
												}"
												:icon="HiSolidChevronRight" />
										</a>
										<CollapseTransition>
											<ul
												v-show="menu.active"
												class="h-auto -mt-0.5 py-1.5 pl-10 list-none overflow-hidden bg-gray-50 dark:bg-gray-700 rounded">
												<li
													v-for="submenu in menu.submenu"
													:key="submenu.id"
													class="transition-all duration-300">
													<Link
														class="m-0 py-1.5 pl-0 group text-sm min-h-8 relative flex items-center font-medium tracking-[.0125em] rounded"
														:href="submenu.url">
														<span
															:class="
																submenu.active
																	? 'text-emerald-500 dark:text-emerald-400'
																	: 'text-gray-700 dark:text-gray-200'
															"
															class="flex-auto group-hover:text-emerald-500 transition-colors inline-block max-w-full text-sm">
															{{ submenu.text }}
														</span>
													</Link>
												</li>
											</ul>
										</CollapseTransition>
									</template>
									<Link
										v-else
										:href="menu.url"
										:class="
											menu.active
												? 'text-emerald-600 dark:text-emerald-400'
												: 'text-gray-900 dark:text-gray-300'
										"
										class="text-base font-normal flex items-center px-3 py-2 hover:bg-emerald-200/50 hover:text-emerald-600 dark:hover:bg-gray-900 group">
										<VueIcon
											:class="
												menu.active
													? 'text-emerald-500  group-hover:text-emerald-700 dark:group-hover:text-emerald-300'
													: 'text-sky-500  group-hover:text-emerald-900 dark:group-hover:text-gray-100'
											"
											class="w-6 h-6 flex-shrink-0 transition duration-75 group-hover:text-emerald-700"
											:icon="menu.icon" />
										<span class="ml-3">
											{{ menu.text }}
										</span>
									</Link>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</aside>
			<div
				class="bg-gray-900 opacity-50 fixed inset-0 z-10"
				:class="{ hidden: !showSidebar }"></div>
			<div
				class="h-full w-full bg-gray-100 dark:bg-gray-900 relative overflow-y-auto lg:ml-64">
				<slot />
			</div>
		</div>
	</body>
</template>
