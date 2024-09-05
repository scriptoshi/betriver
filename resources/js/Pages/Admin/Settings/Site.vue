<script setup>
	import { ref, watch } from "vue";

	import { router, useForm } from "@inertiajs/vue3";
	import { ArrowRightSquareIcon } from "lucide-vue-next";
	import {
		HiRefresh,
		HiSolidChevronDown,
		IoCogSharp,
		LaUsersCogSolid,
		MdSettingsOutlined,
	} from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import LogoInput from "@/Components/LogoInput.vue";
	import LogoInputLocal from "@/Components/LogoInputLocal.vue";
	import MultiSelect from "@/Components/Multiselect/Multiselect.vue";
	import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	const disks = [
		{ key: "public", value: "public", label: "Local Server" },
		{ key: "do", value: "do", label: "Digitalocean S3 Spaces" },
		{ key: "aws", value: "aws", label: "AWS S3 storage" },
	];
	const props = defineProps({
		settings: Object,
		timezones: Object,
	});

	const form = useForm({
		group: "site",
		app_name: "Betriver",
		uploads_disk: "public",
		profile_photo_disk: "public",
		description: "Advanced Crypto Betting for the next generation",
		logo: null,
		currency_code: "USD",
		odds_spread: 0.2,
		currency_symbol: "$",
		currency_display: "auto",
		timezone: "UTC",
		enable_exchange: true,
		enable_bookie: true,
		exchange_max_bet: 1000,
		exchange_min_bet: 1,
		bookie_max_bet: 500,
		bookie_min_bet: 500,
		enable_kyc: true,
		sms_gateway: null,
		logo_path: null,
		logo_upload: false,
		logo_uri: null,
		coincap_apikey: null,
		apifootball_api_key: null,
		...props.settings,
	});
	const saving = ref(1);
	const saveOne = () => {
		saving.value = 1;
		form.post(window.route("admin.settings.store"));
	};
	const saveTwo = () => {
		saving.value = 2;
		form.post(window.route("admin.settings.store"));
	};
	watch(
		[
			() => form.enable_bookie,
			() => form.enable_exchange,
			() => form.enable_kyc,
		],
		([enableBookie, enableExchange, enableKyc]) => {
			form.post(window.route("admin.settings.store"));
		},
	);
	const reloading = ref(false);
	const reload = () => {
		reloading.value = true;
		router.reload({ onFinish: () => (reloading.value = false) });
	};
</script>
<template>
	<Head title="Site settings" />
	<AdminLayout>
		<div class="container py-8">
			<div class="grid sm:grid-cols-2">
				<div>
					<div class="flex items-center space-x-1">
						<VueIcon
							:icon="MdSettingsOutlined"
							class="w-6 h-6 text-emerald-500" />
						<h1
							class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
							Site Settings
						</h1>
					</div>
					<p class="text-gray-600 dark:text-gray-300 mb-4">
						Configure Site and User interface settings
					</p>
				</div>
				<div class="flex justify-end items-center">
					<div class="px-2 sm:px-5 text-center dark:border-gray-600">
						<Switch v-model="form.enable_exchange">
							{{ $t("Exchange") }}
						</Switch>
						<p v-if="form.enable_exchange" class="text-gray-400">
							Exchange Active
						</p>
						<p v-else class="text-gray-400">Exchange Disabled</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<Switch v-model="form.enable_bookie">
							{{ $t("Bookie") }}
						</Switch>
						<p v-if="form.enable_exchange" class="text-gray-400">
							Bookie Active
						</p>
						<p v-else class="text-gray-400">Bookie Disabled</p>
					</div>
					<div
						class="px-2 sm:px-5 text-center border-l-2 border-gray-300 dark:border-gray-600">
						<Switch v-model="form.enable_kyc">
							{{ $t("KYC") }}
						</Switch>
						<p v-if="form.enable_kyc" class="text-gray-400">
							KYC Active
						</p>
						<p v-else class="text-gray-400">KYC Disabled</p>
					</div>
				</div>
			</div>
			<div class="grid sm:grid-cols-2 gap-5 mt-8">
				<div class="card">
					<div class="card-body p-6 grid gap-4">
						<div class="flex justify-between items-center">
							<div class="flex items-center">
								<VueIcon
									:icon="IoCogSharp"
									class="mr-2 h-6 w-6 text-sky-600 dark:text-sky-400" />
								<h3
									class="text-lg !text-sky-600 dark:!text-sky-400">
									Site Config
								</h3>
							</div>
							<a
								@click.prevent="reload"
								v-tippy="$t('Reload')"
								href="#">
								<VueIcon
									:class="{ 'animate-spin': reloading }"
									:icon="HiRefresh" />
							</a>
						</div>
						<FormInput label="App Name" v-model="form.app_name" />
						<FormInput
							label="Site Description"
							v-model="form.description" />
						<div>
							<div
								class="gap-x-3 sm:col-span-2 grid md:grid-cols-2">
								<FormInput
									v-model="form.logo_uri"
									:disabled="form.logo_upload"
									placeholder="https://"
									:error="form.errors.logo_uri"
									:help="$t('Supports png, jpeg or svg')">
									<template #label>
										<div class="flex">
											<span class="mr-3">
												{{ $t("Logo") }}
											</span>
											<label
												class="inline-flex items-center space-x-2">
												<input
													v-model="form.logo_upload"
													class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
													type="checkbox" />
												<span>
													{{ $t("Upload to server") }}
												</span>
											</label>
										</div>
									</template>
								</FormInput>
								<template v-if="form.logo_upload">
									<LogoInput
										v-if="settings.uploads_disk != 'public'"
										v-model="form.logo_uri"
										v-model:file="form.logo_path"
										auto />
									<LogoInputLocal
										v-else
										v-model="form.logo_uri"
										v-model:file="form.logo_path" />
								</template>
								<img
									v-else
									class="w-12 h-12 my-auto rounded-full b-0"
									:src="form.logo_uri ?? fakeLogo" />
							</div>
							<p v-if="form.errors.logo" class="text-red-500">
								{{ form.errors.logo }}
							</p>
							<p v-else class="text-xs">
								{{ $t("") }}
							</p>
						</div>

						<div>
							<FormLabel class="mb-2">
								{{ $t("Uploads Disk") }}
							</FormLabel>
							<MultiSelect
								:options="disks"
								valueProp="value"
								label="label"
								v-model="form.uploads_disk"
								searchable
								closeOnSelect>
								<template #caret="{ isOpen }">
									<VueIcon
										:class="{ 'rotate-180': isOpen }"
										class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
										:icon="HiSolidChevronDown" />
								</template>
							</MultiSelect>
							<p>
								If you set to s3 based disk, Add your
								credentials to the .env file!
							</p>
						</div>
						<div>
							<FormLabel class="mb-2">
								{{ $t("Profile photo upload disk") }}
							</FormLabel>
							<MultiSelect
								:options="disks"
								valueProp="value"
								label="label"
								v-model="form.profile_photo_disk"
								searchable
								closeOnSelect>
								<template #caret="{ isOpen }">
									<VueIcon
										:class="{ 'rotate-180': isOpen }"
										class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
										:icon="HiSolidChevronDown" />
								</template>
							</MultiSelect>
							<p>
								If you set to s3 based disk, Add your
								credentials to the .env file!
							</p>
						</div>
						<div>
							<FormLabel class="mb-2">
								{{ $t("Time zones") }}
							</FormLabel>
							<MultiSelect
								:options="timezones"
								valueProp="value"
								label="label"
								v-model="form.timezone"
								searchable
								closeOnSelect>
								<template #caret="{ isOpen }">
									<VueIcon
										:class="{ 'rotate-180': isOpen }"
										class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
										:icon="HiSolidChevronDown" />
								</template>
							</MultiSelect>
						</div>
						<FormInput
							label="Odd Spread. Groups odds in ranges on the spread"
							v-model="form.odds_spread"
							help="Larger size good for admin profit, but is bad for the exchange, so balance it out" />
						<div class="flex justify-end">
							<PrimaryButton @click="saveOne" primary>
								<Loading
									class="mr-2 -ml-1"
									v-if="form.processing && saving == 1" />
								Update Settings
							</PrimaryButton>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-body p-6 grid gap-4">
						<div class="flex justify-between items-center">
							<div class="flex items-center">
								<VueIcon
									:icon="LaUsersCogSolid"
									class="mr-2 h-6 w-6 text-sky-600 dark:text-sky-400" />
								<h3
									class="text-lg !text-sky-600 dark:!text-sky-400">
									Bettings Config
								</h3>
							</div>
							<a
								@click.prevent="reload"
								v-tippy="$t('Reload')"
								href="#">
								<VueIcon
									:class="{ 'animate-spin': reloading }"
									:icon="HiRefresh" />
							</a>
						</div>
						<div class="grid grid-cols-2 gap-3">
							<FormInput
								label="Site Currency"
								v-model="form.currency_code" />
							<FormInput
								label="Currency Symbol"
								v-model="form.currency_symbol" />
						</div>
						<div>
							<FormLabel class="mb-2">
								{{ $t("Currency Disply Style") }}
							</FormLabel>
							<MultiSelect
								:options="[
									{
										label: 'Symbol Only Eg 7.5$',
										value: 'symbol',
									},
									{
										label: 'Currency Code Eg 7.5USD',
										value: 'code',
									},
									{
										label: 'Auto-select as needed',
										value: 'auto',
									},
								]"
								valueProp="value"
								label="label"
								v-model="form.currency_display"
								searchable
								closeOnSelect>
								<template #caret="{ isOpen }">
									<VueIcon
										:class="{ 'rotate-180': isOpen }"
										class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
										:icon="HiSolidChevronDown" />
								</template>
							</MultiSelect>
						</div>
						<div>
							<FormInput
								label="Coincap (Crypto & Currency rates) Api key"
								v-model="form.coincap_apikey" />
							<a
								target="_blank"
								class="text-sky-600 text-xs mt-1 dark:text-sky-400 hover:text-sky-500"
								href="https://coincap.io/api-key">
								Get it free here
								<ArrowRightSquareIcon
									class="w-3 h-3 ml-1 inline-flex" />
							</a>
						</div>

						<div class="grid sm:grid-cols-2 gap-4">
							<FormInput
								label="Exchange Maximum Bet"
								v-model="form.exchange_max_bet"
								:help="$t('Max users can bet against others')">
								<template #trail>
									{{ settings.currency_code }}
								</template>
							</FormInput>
							<FormInput
								label="Exchange Minimum Bet"
								v-model="form.exchange_min_bet"
								:help="
									$t('Min users can bet against each other')
								">
								<template #trail>
									{{ settings.currency_code }}
								</template>
							</FormInput>
						</div>
						<div class="grid sm:grid-cols-2 gap-4">
							<FormInput
								label="Bookie Maximum Bet"
								v-model="form.bookie_max_bet"
								:help="$t('Max users can bet against you')">
								<template #trail>
									{{ settings.currency_code }}
								</template>
							</FormInput>
							<FormInput
								label="Bookie Minimum Bet"
								v-model="form.bookie_min_bet"
								:help="$t('Min users can bet against you')">
								<template #trail>
									{{ settings.currency_code }}
								</template>
							</FormInput>
						</div>
						<div
							class="w-full border mt-4 dark:border-gray-600 p-6 rounded-sm">
							<h3 class="text-emerald-500">API FOOTBALL KEY</h3>
							<p class="mb-2">
								Get your key here.
								<a
									target="_blank"
									class="text-sky-500 dark:hover:text-sky-300 hover:text-sky-700"
									href="https://dashboard.api-football.com/profile?access">
									https://dashboard.api-football.com
								</a>
							</p>
							<FormInput
								v-model="form.apifootball_api_key"
								:help="
									$t(
										'The free key is too limitted for more than 10 games.',
									)
								"></FormInput>
						</div>
						<div class="flex justify-end pt-5">
							<PrimaryButton @click="saveTwo" primary>
								<Loading
									v-if="form.processing && saving == 2"
									class="mr-2 -ml-1" />
								Update Settings
							</PrimaryButton>
						</div>
					</div>
				</div>
			</div>
		</div>
	</AdminLayout>
</template>
