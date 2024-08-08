<script setup>
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import { Link, useForm } from "@inertiajs/vue3";
	import { HiArrowLeft } from "oh-vue-icons/icons";
	const props = defineProps({
		title: { required: false, type: String },
		stake: { type: Object, required: true },
	});
	const form = useForm({
		slip_id: props.stake.slip_id,
		user_id: props.stake.user_id,
		bet_id: props.stake.bet_id,
		game_id: props.stake.game_id,
		uuid: props.stake.uuid,
		scoreType: props.stake.scoreType,
		amount: props.stake.amount,
		filled: props.stake.filled,
		unfilled: props.stake.unfilled,
		payout: props.stake.payout,
		odds: props.stake.odds,
		status: props.stake.status,
		won: props.stake.won,
		is_withdrawn: props.stake.is_withdrawn,
		allow_partial: props.stake.allow_partial,
	});
	const save = () => form.put(window.route("stakes.edit", props.stake.id));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Edit Stake`" />
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Edit Stake</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.stakes.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to stakes list") }}
							</Link>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5">
								<FormInput
									:label="Slip_id"
									v-model="form.slip_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.slip_id" />

								<FormInput
									:label="User_id"
									v-model="form.user_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.user_id" />

								<FormInput
									:label="Bet_id"
									v-model="form.bet_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.bet_id" />

								<FormInput
									:label="Game_id"
									v-model="form.game_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.game_id" />

								<FormInput
									:label="ScoreType"
									v-model="form.scoreType"
									class="col-span-3"
									type="text"
									:error="form.errors.scoreType" />

								<FormInput
									:label="Amount"
									v-model="form.amount"
									class="col-span-3"
									:type="number"
									:error="form.errors.amount" />

								<FormInput
									:label="Filled"
									v-model="form.filled"
									class="col-span-3"
									:type="number"
									:error="form.errors.filled" />

								<FormInput
									:label="Unfilled"
									v-model="form.unfilled"
									class="col-span-3"
									:type="number"
									:error="form.errors.unfilled" />

								<FormInput
									:label="Payout"
									v-model="form.payout"
									class="col-span-3"
									:type="number"
									:error="form.errors.payout" />

								<FormInput
									:label="Odds"
									v-model="form.odds"
									class="col-span-3"
									:type="number"
									:error="form.errors.odds" />

								<div class="pt-12">
									<div class="flex justify-end">
										<PrimaryButton
											as="button"
											:href="route('admin.stakes.index')"
											type="button"
											link
											secondary>
											{{ $t("Cancel") }}
										</PrimaryButton>
										<PrimaryButton
											type="submit"
											:disabled="form.processing"
											class="mt-4"
											primary>
											<Loading
												class="mr-2 -ml-1 inline-block w-5 h-5"
												v-if="form.processing" />
											<span class="text-sm text-white">
												{{ $t("Update Stake") }}
											</span>
										</PrimaryButton>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
