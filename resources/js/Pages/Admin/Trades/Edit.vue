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
		trade: { type: Object, required: true },
	});
	const form = useForm({
		maker_id: props.trade.maker_id,
		taker_id: props.trade.taker_id,
		amount: props.trade.amount,
		buy: props.trade.buy,
		sell: props.trade.sell,
		margin: props.trade.margin,
	});
	const save = () => form.put(window.route("trades.edit", props.trade.id));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Edit Trade`" />
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Edit Trade</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.trades.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to trades list") }}
							</Link>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5">
								<FormInput
									:label="Maker_id"
									v-model="form.maker_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.maker_id" />

								<FormInput
									:label="Taker_id"
									v-model="form.taker_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.taker_id" />

								<FormInput
									:label="Amount"
									v-model="form.amount"
									class="col-span-3"
									:type="number"
									:error="form.errors.amount" />

								<FormInput
									:label="Buy"
									v-model="form.buy"
									class="col-span-3"
									:type="number"
									:error="form.errors.buy" />

								<FormInput
									:label="Sell"
									v-model="form.sell"
									class="col-span-3"
									:type="number"
									:error="form.errors.sell" />

								<FormInput
									:label="Margin"
									v-model="form.margin"
									class="col-span-3"
									:type="number"
									:error="form.errors.margin" />

								<div class="pt-12">
									<div class="flex justify-end">
										<PrimaryButton
											as="button"
											:href="route('admin.trades.index')"
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
												{{ $t("Update Trade") }}
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
