<script setup>
	import { computed } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { AlertCircle, Minus, Plus } from "lucide-vue-next";

	import MoneyFormat from "@/Components/MoneyFormat.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import { Alert, AlertDescription, AlertTitle } from "@/Components/ui/alert";
	import { useBookieForm } from "@/Pages/Games/bettingForm";

	const { bookieForm } = useBookieForm();
	function calculateBetReturn(stake, price, isLay) {
		if (stake < 0 || price < 1) {
			return {
				liability: null,
				payout: null,
			};
		}
		let liability;
		if (!isLay) {
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
	const form = useForm({
		stake: 0,
		isLay: true, // for now only buying, sell will come later
	});
	const totalPrice = computed(() =>
		Object.values(bookieForm.value).reduce(
			(sum, bet) => sum + parseFloat(bet.price ?? 0),
			0,
		),
	);
	const info = computed(() => {
		return calculateBetReturn(
			parseFloat(form.stake ?? 0),
			parseFloat(totalPrice.value ?? 0),
			form.isLay,
		);
	});
</script>

<template>
	<div class="flex-1 px-2.5 pt-4 pb-2">
		<div v-if="totalPrice <= 0" class="my-4">
			<Alert
				variant="secondary"
				class="border border-gray-250 dark:border-gray-700">
				<AlertCircle class="w-4 h-4" />
				<AlertTitle>Add some bets to start</AlertTitle>
				<AlertDescription>
					To create a multiple, you need at least 2 back selections.
				</AlertDescription>
			</Alert>
		</div>
		<div
			v-else
			class="grid grid-cols-[75fr_75fr_70fr] gap-[5px] items-stretch">
			<span class="min-w-0">
				<div
					class="flex overflow-hidden border border-gray-200 dark:border-gray-600 cursor-text h-9 rounded-sm">
					<div
						class="relative flex-1 text-sm font-bold overflow-hidden">
						<input
							type="text"
							placeholder=""
							v-model="form.stake"
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
								form.stake = parseFloat(form.stake ?? 0) + 1
							"
							class="flex-1 flex items-center justify-center text-emerald-600 dark:text-emerald-400 min-w-[18px] min-h-0 bg-transparent outline-none cursor-pointer p-0 rounded-[1px] border-[none]"
							tabindex="-1">
							<Plus class="w-3 h-3 stroke-[3px]" />
						</button>
						<button
							type="button"
							@click="
								form.stake =
									parseFloat(form.stake) - 1 < 0
										? 0
										: parseFloat(form.stake) - 1
							"
							:disabled="parseFloat(form.stake) <= 0"
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
						<input
							type="text"
							placeholder=""
							class="text-gray-800 dark:text-white peer delay-[50ms] absolute w-full h-full outline-none [background:none] leading-[1.15] text-[100%] overflow-visible font-bold whitespace-nowrap pl-1 pt-[15px] p-0.5 rounded-none border-[none] left-0 top-0"
							maxlength="11"
							tabindex="0"
							:value="totalPrice"
							disabled />
						<span
							class="absolute uppercase font-bold text-[11px] origin-[left_top] whitespace-nowrap overflow-hidden max-w-full text-ellipsis select-none pointer-events-none left-0.5 duration-300 transform -translate-y-2 scale-75 top-2 peer-focus:text-emerald-600 peer-focus:dark:text-emerald-400 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-2 start-1">
							Price
						</span>
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
		</div>

		<div class="mt-4">
			<PrimaryButton
				:disabled="totalPrice <= 0 || form.stake <= 0"
				class="w-full"
				primary>
				BUY
			</PrimaryButton>
		</div>
	</div>
</template>
<style scoped>
	* {
		@apply box-border shrink-0 flex-wrap;
	}
</style>
