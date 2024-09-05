<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { XIcon } from "lucide-vue-next";
	import { HiSolidChevronDown } from "oh-vue-icons/icons";

	import CurrencySymbol from "@/Components/CurrencySymbol.vue";
	import Flag from "@/Components/Flag";
	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		gateway: String,
		config: Object,
		currencies: Object,
		selected: Array,
	});
	const form = useForm({
		gateway: props.gateway,
		max_withdraw_limit: null,
		min_withdraw_limit: null,
		auto_approve_withdraw_address: false,
		...props.config,
		currencies: [...props.selected],
	});

	const save = () => {
		form.post(window.route("admin.settings.payments.store"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
	const settings = Object.keys(props.config);
	function titleCase(str) {
		return str
			.split("_")
			.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
			.join(" ");
	}
</script>
<template>
	<div class="grid gap-4 p-6 border dark:border-gray-600 rounded-sm">
		<h3
			class="text-xl flex items-center text-emerald-500 dark:text-emerald-400 mb-4">
			<img :src="config.logo" class="w-6 h-6 mr-6" />
			{{ titleCase(gateway) }} Settings
		</h3>
		<template v-for="setting in settings" :key="setting">
			<FormInput
				label="Gateway Name"
				v-if="setting === 'name'"
				:error="form.errors.name"
				v-model="form.name" />
			<FormInput
				label="Gateway Logo"
				v-else-if="setting === 'logo'"
				:error="form.errors.logo"
				v-model="form.logo" />
			<Switch
				v-else-if="setting === 'enable_withdraw'"
				v-model="form.enable_withdraw">
				Enable Gateway for Withdraw
			</Switch>
			<Switch
				v-else-if="setting === 'enable_deposit'"
				v-model="form.enable_deposit">
				Enable Gateway for deposits
			</Switch>
			<div v-else-if="setting === 'webhook_id'">
				<FormInput
					label="Webhook ID"
					:error="form.errors.webhook_id"
					v-model="form.webhook_id" />
			</div>
			<FormInput
				v-else-if="
					!['max_withdraw_limit', 'min_withdraw_limit'].includes(
						setting,
					)
				"
				:label="titleCase(setting)"
				:error="form.errors[setting]"
				v-model="form[setting]" />
		</template>
		<div class="w-full">
			<FormLabel class="mb-2">Gateway Currencies</FormLabel>
			<Multiselect
				v-model="form.currencies"
				:options="currencies"
				@tag="() => null"
				mode="tags"
				:addTagOn="false"
				searchable
				tag-placeholder="Add this as new tag"
				placeholder="Type to search or add currency"
				label="label"
				track-by="label">
				<template #caret="{ isOpen }">
					<VueIcon
						:class="{ 'rotate-180': isOpen }"
						class="mr-3 relative z-10 opacity-60 text-gray-400 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
						:icon="HiSolidChevronDown" />
				</template>
				<template #option="{ option }">
					<div class="flex flex-row items-center">
						<Flag
							v-if="option.img.length === 3"
							class="w-7 h-7 rounded-full mr-2"
							:iso="option.img" />
						<img
							v-else
							class="mr-2 flex-shrink-0 rounded-full flex-grow-0 w-6 h-6"
							:src="option.img" />
						<div>
							<div class="font-semibold">{{ option.label }}</div>
							<div class="opacity-90 font-semibold text-xs">
								{{ option.code }}
							</div>
						</div>
					</div>
				</template>
				<template
					#tag="{
						option,
						handleTagRemove,
						disabled,
						classList,
						ariaTagLabel,
						localize,
					}">
					<span
						:class="[
							classList.tag,
							option.disabled ? classList.tagDisabled : null,
						]"
						tabindex="-1"
						@keyup.enter="handleTagRemove(option, $event)"
						:key="key"
						:aria-label="ariaTagLabel(localize(option[label]))">
						<Flag
							v-if="option.img.length === 3"
							class="w-4 h-4 rounded-full mr-2"
							:iso="option.img" />
						<img
							v-else
							class="mr-2 flex-shrink-0 rounded-full flex-grow-0 w-4 h-4"
							:src="option.img" />
						{{ localize(option.label) }}
						<span
							v-if="!disabled && !option.disabled"
							:class="classList.tagRemove"
							@click.stop="handleTagRemove(option, $event)">
							<XIcon
								class="text-emerald-500 dark:text-emerald-400 stroke-2 inline-block w-3.5 h-3.5" />
						</span>
					</span>
				</template>
			</Multiselect>
		</div>
		<div
			class="grid border-t border-gray-150 dark:border-gray-700 pt-3 mt-3 gap-3 sm:grid-cols-2">
			<FormInput
				label="Minimum Allowed Withdraw"
				:error="form.errors.min_withdraw_limit"
				v-model="form.min_withdraw_limit">
				<template #trail>
					<CurrencySymbol class="text-xs font-semibold" />
				</template>
			</FormInput>
			<FormInput
				label="Maximum Allowed Withdraw"
				:error="form.errors.max_withdraw_limit"
				v-model="form.max_withdraw_limit">
				<template #trail>
					<CurrencySymbol class="text-xs font-semibold" />
				</template>
			</FormInput>
			<p class="sm:col-span-2">
				Use zero for unlimited allowed Withdraw !
			</p>
		</div>
		<p v-if="gateway == 'nowpayments'" class="text-sm">
			Now payments requires manual submission of withdraw addresses for
			whitelisting to support
		</p>
		<Switch v-model="form.auto_approve_withdraw_address">
			Auto approve withdraw address
		</Switch>
		<div class="grid">
			<a
				v-if="gateway == 'coinpayments'"
				target="_blank"
				class="text-sky-500 hover:text-sky-600 dark:hover:text-sky-400 underline"
				href="https://www.coinpayments.net/index.php?cmd=acct_settings">
				Get your merchant ID and IPN secret here
			</a>
			<a
				v-if="gateway == 'coinpayments'"
				target="_blank"
				class="text-sky-500 hover:text-sky-600 dark:hover:text-sky-400 underline"
				href="https://www.coinpayments.net/acct-api-keys">
				Get your Publickey and Privatekey here
			</a>
		</div>
		<div class="flex items-end justify-end">
			<PrimaryButton
				:disabled="form.processing"
				@click="save"
				class="mt-4"
				primary>
				<Loading class="mr-2 -ml-1" v-if="form.processing" />
				Save {{ titleCase(gateway) }} Settings
			</PrimaryButton>
		</div>
	</div>
</template>
