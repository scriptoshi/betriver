<script setup>
	import { computed, ref } from "vue";

	import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
	import { Link as InertiaLink, useForm, usePage } from "@inertiajs/vue3";
	import {
		HiSolidChevronDown,
		MdAccountcircleTwotone,
		MdCreditcard,
		MdExittoappRound,
		MdGeneratingtokensOutlined,
		MdInsertchartoutlinedRound,
		MdMessageOutlined,
		MdSettingsOutlined,
		RiFileList2Line,
	} from "oh-vue-icons/icons";
	import { useI18n } from "vue-i18n";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { ucfirst } from "@/hooks";
	import LangPicker from "@/Layouts/FontendLayout/Dropdowns/Lang.vue";

	const user = computed(() => usePage().props.auth.user);
	defineEmits(["feedbackModal"]);
	const { t } = useI18n();
	const userNavigation = [
		{
			name: t("Statement"),
			href: window.route("accounts.statement"),
			icon: RiFileList2Line,
		},
		{
			name: t("Payout Whitelist"),
			href: window.route("whitelists.index"),
			icon: MdGeneratingtokensOutlined,
		},
		{
			name: t("Referral Earnings"),
			href: window.route("accounts.referrals"),
			icon: MdAccountcircleTwotone,
		},

		{
			name: t("Settings"),
			href: window.route("accounts.settings"),
			icon: MdSettingsOutlined,
			active: false,
		},
		{
			name: t("Stats & Commissions"),
			href: window.route("accounts.commission"),
			icon: MdInsertchartoutlinedRound,
			active: false,
		},
		{
			name: t("KYC Verification"),
			href: window.route("accounts.verify"),
			icon: MdCreditcard,
		},
	];
	const showOddsMenu = ref(false);
	const oddsActivating = ref(null);
	const oddsForm = useForm({ oddsType: null });
	const updateOddType = (oddsType) => {
		oddsActivating.value = oddsType;
		oddsForm
			.transform((d) => ({ oddsType }))
			.post(window.route("favourites.odds"), {
				onFinish() {
					oddsActivating.value = null;
				},
			});
	};
	const hideBalanceForm = useForm({ hideBalance: null });
	const hideBalance = (hide) => {
		hideBalanceForm
			.transform((d) => ({ hideBalance: hide }))
			.post(window.route("favourites.hide.balance"));
	};
</script>
<template>
	<Menu v-slot="{ open }" as="div" class="relative dropdown h-full">
		<MenuButton
			:class="
				open
					? 'bg-gray-100 dark:bg-gray-800 text-emerald-500'
					: 'bg-transparent text-gray-800 dark:text-gray-100'
			"
			class="h-full px-5 flex items-center hover:bg-gray-150 dark:hover:bg-gray-750">
			<div class="flex items-center w-7">
				<img
					:src="user.profile_photo_url"
					class="w-7 h-7 rounded-full" />
			</div>
			<div
				class="font-inter ml-5 hidden md:flex flex-col text-start leading-3">
				<div class="uppercase text-[10px] mb-0.5 leading-3">
					{{ $t("Balance") }}
				</div>
				<h3
					v-if="user.hide_balance"
					class="text-sm font-bold leading-3">
					---
				</h3>
				<h3 v-else class="text-sm font-bold leading-3">
					<MoneyFormat :amount="user.balance" />
				</h3>
			</div>
			<div class="hidden md:flex items-center">
				<VueIcon class="w-5 h-5 ml-4" :icon="HiSolidChevronDown" />
			</div>
		</MenuButton>
		<transition
			enter-active-class="transition ease-out duration-200"
			enter-from-class="transform opacity-0 scale-95"
			enter-to-class="transform opacity-100 scale-100"
			leave-active-class="transition ease-in duration-75"
			leave-from-class="transform opacity-100 scale-100"
			leave-to-class="transform opacity-0 scale-95">
			<MenuItems
				class="aside bottom place-left tracking-wider pb-3 text-xs mt-1 origin-top-right absolute right-0 min-w-[275px] rounded-sm with-shadow bg-white dark:bg-gray-750 focus:outline-none">
				<div
					class="px-6 pb-3 pt-5 font-inter bg-gray-100 dark:bg-gray-700">
					<div
						class="border-b pb-1 border-gray-250 dark:border-gray-550">
						<div class="flex items-center justify-between">
							<h3
								class="text-[11px] opacity-85 font-semibold uppercase">
								{{ $t("Total Exposure") }}
							</h3>
							<h3
								class="text-xs text-gray-700 dark:text-gray-100 font-semibold">
								<MoneyFormat :amount="$page.props.exposure" />
							</h3>
						</div>
						<div class="flex items-center justify-between">
							<h3
								class="text-[11px] opacity-85 font-semibold uppercase">
								{{ $t("PONTENTIAL WINNINGS") }}
							</h3>
							<h3
								class="text-xs text-gray-700 dark:text-gray-100 font-semibold">
								<MoneyFormat
									:amount="$page.props.potentialWinnings" />
							</h3>
						</div>
					</div>
					<div class="grid grid-cols-2 mt-3 gap-3">
						<PrimaryButton
							link
							:href="route('deposits.create')"
							class="w-full">
							{{ $t("DEPOSIT") }}
						</PrimaryButton>
						<PrimaryButton
							link
							:href="route('withdraws.create')"
							class="w-full">
							{{ $t("WITHDRAW") }}
						</PrimaryButton>
					</div>
				</div>

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
							<VueIcon
								:icon="item.icon"
								class="mr-3 h-6 w-6 text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
								aria-hidden="true" />
							{{ item.name }}
						</InertiaLink>
					</MenuItem>
					<MenuItem v-slot="{ active }">
						<a
							href="#"
							@click="$emit('feedbackModal')"
							:class="[
								active
									? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-100'
									: 'text-gray-700 dark:text-gray-200 ',
								'group flex items-center px-6 py-2.5 text-base hover:bg-emerald-gray-300 dark:hover:bg-gray-700',
							]">
							<VueIcon
								:icon="MdMessageOutlined"
								class="mr-3 h-6 w-6 text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
								aria-hidden="true" />
							{{ $t("Feedback") }}
						</a>
					</MenuItem>
					<MenuItem v-slot="{ active }">
						<InertiaLink
							:href="route('logout')"
							method="post"
							as="button"
							:class="[
								active
									? 'bg-gray-100 dark:bg-gray-500 text-gray-900 dark:text-gray-100'
									: 'text-gray-700 dark:text-gray-200 ',
								'group w-full flex items-center px-6 py-2.5 text-base hover:bg-emerald-gray-300 dark:hover:bg-gray-700',
							]">
							<VueIcon
								:icon="MdExittoappRound"
								class="mr-3 h-6 w-6 text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-300"
								aria-hidden="true" />
							{{ $t("Logout") }}
						</InertiaLink>
					</MenuItem>
					<div>
						<a
							href="#"
							@click.stop="showOddsMenu = !showOddsMenu"
							class="group flex justify-between items-center px-6 py-3 border-t border-gray-150 dark:border-gray-650 text-xs hover:bg-emerald-gray-300 dark:hover:bg-gray-700">
							<div
								:class="
									showOddsMenu
										? 'text-emerald-600 font-semibold dark:text-emerald-400'
										: 'text-gray-700 dark:text-gray-200'
								">
								Odds Type
							</div>
							<div
								class="flex items-center font-semibold space-x-2 dark:text-white text-gray-750">
								<div>{{ ucfirst(user.odds_type) }}</div>
								<VueIcon
									:icon="HiSolidChevronDown"
									class="w-4 h-4 transition-transform duration-300"
									:class="{ 'rotate-180': showOddsMenu }" />
							</div>
						</a>
						<CollapseTransition>
							<div v-show="showOddsMenu" class="py-2 px-6">
								<RadioSelect
									:grid="1"
									:modelValue="user.odds_type"
									@update:modelValue="updateOddType"
									:options="[
										{
											value: 'decimal',
											label: 'Decimal',
											loading:
												oddsActivating === 'decimal',
										},
										{
											value: 'american',
											label: 'American',
											loading:
												oddsActivating === 'american',
										},
										{
											value: 'percentage',
											label: 'Percentage',
											loading:
												oddsActivating === 'percentage',
										},
									]" />
							</div>
						</CollapseTransition>
					</div>
					<div class="">
						<LangPicker expanded />
					</div>
					<div class="px-6 py-2">
						<Switch
							@update:modelValue="hideBalance"
							:modelValue="user.hide_balance">
							Hide account balance
						</Switch>
					</div>
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
