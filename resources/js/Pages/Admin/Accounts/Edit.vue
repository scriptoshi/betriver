<script setup>
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import MultiSelect from "@/Components/MultiSelect/MultiSelect.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import { Link, useForm } from "@inertiajs/vue3";
	import { HiArrowLeft, HiSolidChevronDown } from "oh-vue-icons/icons";
	const props = defineProps({
		title: { required: false, type: String },
		account: { type: Object, required: true },
	});
	const form = useForm({
		accountable: props.account.accountable,
		type: props.account.type,
		amount: props.account.amount,
	});
	const save = () =>
		form.put(window.route("accounts.edit", props.account.id));
</script>
<template>
	<AdminLayout>
		<Head :title="title ?? `Edit Account`" />
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Edit Account</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.accounts.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to accounts list") }}
							</Link>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form
								@submit.prevent="save"
								class="container lg:w-4/5">
								<div>
									<FormLabel class="mb-1">
										{{ $t("Type") }}
									</FormLabel>
									<MultiSelect
										:options="[
											{
												key: 'wins',
												value: 'wins',
												label: 'Bookie Wins',
											},
											{
												key: 'loss',
												value: 'loss',
												label: 'Bookie Losses',
											},
											{
												key: 'arbitrage',
												value: 'arbitrage',
												label: 'Exchange arbitrage',
											},
											{
												key: 'fees',
												value: 'fees',
												label: 'Exchange fees',
											},
										]"
										valueProp="value"
										label="label"
										:placeholder="$t('')"
										v-model="type"
										searchable
										closeOnSelect
										object>
										<template #caret="{ isOpen }">
											<VueIcon
												:class="{
													'rotate-180': isOpen,
												}"
												class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
												:icon="HiSolidChevronDown" />
										</template>
									</MultiSelect>
								</div>

								<FormInput
									:label="Amount"
									v-model="form.amount"
									class="col-span-3"
									:type="number"
									:error="form.errors.amount" />

								<div class="pt-12">
									<div class="flex justify-end">
										<PrimaryButton
											as="button"
											:href="
												route('admin.accounts.index')
											"
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
												{{ $t("Update Account") }}
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
