<script setup>
	import { computed } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { Minus, Plus, XIcon } from "lucide-vue-next";

	import OddInput from "@/Components/Cards/OddInput.vue";
	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import Loading from "@/Components/Loading.vue";
	import MoneyFormat from "@/Components/MoneyFormat.vue";
	const props = defineProps({
		price: [Number, String],
		stake: [Number, String],
		bet: Object,
	});
	const emit = defineEmits(["update:stake", "update:price", "remove"]);
	function calculateBetReturn(stake, price, isLay) {
		if (stake < 0 || price < 1) {
			return {
				liability: null,
				payout: null,
			};
		}
		let liability;
		if (isLay) {
			liability = stake * (price - 1);
		} else {
			liability = stake;
		}
		const payout = stake * price;
		return {
			liability: Number(liability.toFixed(2)),
			payout: Number(payout.toFixed(2)),
		};
	}
	const info = computed(() =>
		calculateBetReturn(
			parseFloat(props.stake ?? 0),
			parseFloat(props.price ?? 0),
			props.bet.isLay,
		),
	);
	const form = useForm({ ...props.bet });
	const tradeNow = () => {
		form.clearErrors();
		form.transform((data) => ({
			price: props.price,
			stake: props.stake,
			...props.bet,
		})).post(window.route("stakes.store"), {
			onSuccess() {
				emit("remove");
			},
			preserveScroll: true,
			preserveState: true,
		});
	};
	const isBack = computed(() => !props.bet.isLay);
</script>

<template>
	<div class="flex-1 px-2.5 py-2">
		<div class="flex items-center mb-2 relative group">
			<div class="flex flex-1 flex-col">
				<span
					class="flex-1 font-bold text-[13px] min-w-full max-w-0 overflow-hidden text-ellipsis mb-[3px]">
					<a
						class="bg-transparent text-gray-800 dark:text-white font-bold font-inter no-underline"
						href="/event/44024496/sport/football/england-professional-development-league/2024/09/03/13-00/barnsley-u21-vs-bristol-city-u21/">
						{{ bet.game }}
					</a>
				</span>
				<span
					class="text-[10px] font-bold uppercase tracking-[0.8px] min-w-full max-w-0 overflow-hidden text-ellipsis">
					<a
						class="bg-transparent text-gray-400 dark:text-gray-400 no-underline"
						href="/event/44024496/sport/football/england-professional-development-league/2024/09/03/13-00/barnsley-u21-vs-bristol-city-u21/?market=77684490">
						{{ bet.market }}
					</a>
				</span>
			</div>
			<button
				@click="$emit('remove')"
				class="w-[22px] text-gray-600 dark:text-gray-300 hover:text-red-500 dark:hover:text-red-400 sm:opacity-0 group-hover:opacity-100 transition-opacity duration-300 absolute top-1 right-1 bg-gray-100 hover:bg-red-50 dark:bg-gray-600 dark:hover:bg-red-400/20 h-[22px] rounded-full flex items-center justify-center self-start overflow-visible uppercase outline-none p-1 border-none"
				type="button">
				<XIcon class="w-3 h-3" />
			</button>
		</div>
		<div
			class="flex items-center text-xs font-bold justify-between mb-[5px]">
			<div class="flex flex-1">
				<span
					class="min-w-full max-w-0 overflow-hidden text-ellipsis font-bol">
					<span
						v-if="isBack"
						class="text-emerald-600 dark:text-emerald-400">
						{{ $t("For") }}
					</span>
					<span v-else class="text-sky-600 dark:text-sky-400">
						{{ $t("Against") }}
					</span>
					&nbsp;
					<div
						:class="{ 'truncate w-[140px]': !isBack }"
						class="text-gray-800 inline-flex dark:text-white">
						{{ bet.bet }}
					</div>
				</span>
			</div>
			<div v-if="!isBack" class="justify-end">
				<span
					class="min-w-full text-end max-w-0 overflow-hidden text-ellipsis font-bol">
					<span class="text-gray-400 dark:text-gray-400">
						{{ $t("Liability") }}
					</span>
					&nbsp;
					<span class="text-emerald-600 dark:text-emerald-400">
						<MoneyFormat :amount="info.liability ?? 0" />
					</span>
				</span>
			</div>
		</div>
		<div
			class="grid grid-cols-[75fr_75fr_70fr_55fr] gap-[5px] items-stretch">
			<span class="min-w-0">
				<div
					class="flex overflow-hidden border border-gray-200 dark:border-gray-600 cursor-text h-9 rounded-sm">
					<div
						class="relative flex-1 text-sm font-bold overflow-hidden">
						<input
							type="text"
							placeholder=""
							:value="stake"
							@input="$emit('update:stake', $event.target.value)"
							class="text-gray-800 dark:text-white peer delay-[50ms] absolute w-full h-full outline-none [background:none] leading-[1.15] text-[100%] overflow-visible font-bold whitespace-nowrap pl-[17px] pt-[15px] p-0.5 rounded-none border-[none] left-0 top-0"
							maxlength="11"
							tabindex="0" />
						<span
							class="absolute uppercase font-bold text-[11px] origin-[left_top] whitespace-nowrap overflow-hidden max-w-full text-ellipsis select-none pointer-events-none left-0.5 duration-300 transform -translate-y-2 scale-75 top-2 peer-focus:text-emerald-600 peer-focus:dark:text-emerald-400 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-2 start-1">
							{{ $t("Stake") }}
						</span>
						<span
							class="absolute text-gray-800 dark:text-white opacity-100 peer-placeholder-shown:opacity-0 peer-focus:!opacity-100 font-bold [transform:none] pointer-events-none w-[15px] max-w-[15px] overflow-hidden delay-[50ms] left-0.5 bottom-px transition-opacity duration-300">
							{{ $page.props.currency?.currency_symbol ?? $ }}
						</span>
					</div>
					<div
						class="flex border-l divide-y divide-gray-200 dark:divide-gray-600 border-gray-200 dark:border-gray-600 flex-col relative">
						<button
							type="button"
							@click="
								$emit(
									'update:stake',
									parseFloat(stake ?? 0) + 1,
								)
							"
							class="flex-1 flex items-center justify-center text-emerald-600 dark:text-emerald-400 min-w-[18px] min-h-0 bg-transparent outline-none cursor-pointer p-0 rounded-[1px] border-[none]"
							tabindex="-1">
							<Plus class="w-3 h-3 stroke-[3px]" />
						</button>
						<button
							type="button"
							@click="
								$emit(
									'update:stake',
									parseFloat(stake) - 1 < 0
										? 0
										: parseFloat(stake) - 1,
								)
							"
							:disabled="parseFloat(stake) <= 0"
							class="disabled:pointer-events-none flex-1 flex items-center justify-center text-emerald-600 dark:text-emerald-400 min-w-[18px] min-h-0 bg-transparent outline-none cursor-pointer p-0 rounded-[1px] border-[none]"
							tabindex="-1">
							<Minus class="w-3 h-3 stroke-[3px]" />
						</button>
					</div>
				</div>
			</span>
			<span class="min-w-0">
				<div
					class="flex overflow-hidden border border-gray-200 dark:border-gray-600 cursor-text h-9 rounded-sm">
					<div
						class="relative flex-1 text-sm font-bold overflow-hidden">
						<OddInput
							type="text"
							placeholder=""
							class="text-gray-800 dark:text-white peer delay-[50ms] absolute w-full h-full outline-none [background:none] leading-[1.15] text-[100%] overflow-visible font-bold whitespace-nowrap pl-1 pt-[15px] p-0.5 rounded-none border-[none] left-0 top-0"
							maxlength="11"
							tabindex="0"
							:modelValue="price"
							@update:modelValue="
								(val) => $emit('update:price', val)
							" />
						<span
							class="absolute uppercase font-bold text-[11px] origin-[left_top] whitespace-nowrap overflow-hidden max-w-full text-ellipsis select-none pointer-events-none left-0.5 duration-300 transform -translate-y-2 scale-75 top-2 peer-focus:text-emerald-600 peer-focus:dark:text-emerald-400 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-2 start-1">
							Price
						</span>
					</div>
					<div
						class="flex border-l divide-y divide-gray-200 dark:divide-gray-600 border-gray-200 dark:border-gray-600 flex-col relative">
						<button
							type="button"
							@click="
								$emit(
									'update:price',
									parseFloat(price ?? 0) + 1,
								)
							"
							class="flex-1 flex items-center justify-center text-emerald-600 dark:text-emerald-400 min-w-[18px] min-h-0 bg-transparent outline-none cursor-pointer p-0 rounded-[1px] border-[none]"
							tabindex="-1">
							<Plus class="w-3 h-3 stroke-[3px]" />
						</button>
						<button
							type="button"
							@click="
								$emit(
									'update:price',
									parseFloat(price) - 1 > 0
										? 0
										: parseFloat(price) - 1,
								)
							"
							:disabled="parseFloat(price) <= 0"
							class="disabled:pointer-events-none flex-1 flex items-center justify-center text-emerald-600 dark:text-emerald-400 min-w-[18px] min-h-0 bg-transparent outline-none cursor-pointer p-0 rounded-[1px] border-[none]"
							tabindex="-1">
							<Minus class="w-3 h-3 stroke-[3px]" />
						</button>
					</div>
				</div>
			</span>
			<span class="min-w-0">
				<div
					class="flex overflow-hidden border border-gray-200 dark:border-gray-600 cursor-text h-9 rounded-sm">
					<MoneyFormat :amount="info.payout ?? 0" v-slot="{ amount }">
						<div
							class="relative flex-1 text-sm font-bold overflow-hidden">
							<input
								type="text"
								placeholder=""
								disabled
								class="text-gray-800 dark:text-white peer delay-[50ms] absolute w-full h-full outline-none [background:none] leading-[1.15] text-[100%] overflow-visible font-bold whitespace-nowrap pl-1 pt-[15px] p-0.5 rounded-none border-[none] left-0 top-0"
								maxlength="11"
								tabindex="0"
								:value="info.payout ? amount : null" />
							<span
								class="absolute uppercase font-bold text-[11px] origin-[left_top] whitespace-nowrap overflow-hidden max-w-full text-ellipsis select-none pointer-events-none left-0.5 duration-300 transform -translate-y-2 scale-75 top-2 peer-focus:text-emerald-600 peer-focus:dark:text-emerald-400 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-2 start-1">
								Return
							</span>
						</div>
					</MoneyFormat>
				</div>
			</span>
			<span class="min-w-0">
				<button
					@click="tradeNow"
					class="block items-center justify-center transition-colors ease-in-out duration-300 w-full h-full relative text-[0.65rem] leading-[1.15] overflow-visible uppercase [appearance:button] text-center bg-transparent outline-none cursor-pointer whitespace-nowrap font-extrabold font-inter tracking-[1px] text-gray-400 hover:text-white border mx-0 px-2 py-2 rounded-[1px] border-solid border-gray-200 dark:border-gray-600"
					tabindex="0"
					:class="
						isBack
							? form.processing
								? 'bg-emerald-600 dark:bg-emerald-500'
								: 'hover:bg-emerald-600 dark:hover:bg-emerald-500'
							: form.processing
							? 'bg-sky-600 dark:bg-sky-500'
							: 'hover:bg-sky-600 dark:hover:bg-sky-500'
					"
					:disabled="
						form.processing ||
						parseFloat(price) <= 0 ||
						parseFloat(stake) <= 0
					">
					<Loading class="mx-auto !w-4 !h-4" v-if="form.processing" />
					<span
						v-else
						class="text-ellipsis whitespace-nowrap max-w-full overflow-hidden inline-block z-[1]">
						{{
							bet.isAsk
								? "ASK"
								: bet.isBid
								? "BID"
								: isBack
								? $t("Buy")
								: $t("Sell")
						}}
					</span>
				</button>
			</span>
		</div>
		<CollapseTransition>
			<ul
				v-show="form.hasErrors"
				class="mt-1 list-disc ml-3 text-xs font-bold font-inter">
				<li
					v-for="(error, i) in form.errors"
					:key="i"
					class="text-red-500 dark:text-red-400">
					{{ error }}
				</li>
			</ul>
		</CollapseTransition>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
