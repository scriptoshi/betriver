<script setup>
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { ucfirst } from "@/hooks";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import { Link, useForm } from "@inertiajs/vue3";
	import { HiArrowSmLeft } from "oh-vue-icons/icons";
	const props = defineProps({
		title: { required: false, type: String },
		type: String,
		generate: { type: [String, Number], default: 5 },
		commissions: Object,
	});

	const form = useForm({
		type: props.type,
		commissions: [...Array(parseInt(props.generate)).keys()].map((i) => ({
			level: i + 1,
			percent:
				(props.commissions?.[i + 1] ?? (props.generate - i) / 100) * 1,
		})),
	});
	const save = () => form.post(window.route("admin.commissions.store"));
</script>
<template>
	<Head :title="title ?? `New Commission`" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">{{ ucfirst(type) }} Commission</h3>
							<p>
								Rebuild {{ ucfirst(type) }} Commission Structure
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="
									route('admin.commissions.index', { type })
								"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-sm border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
								<VueIcon
									:icon="HiArrowSmLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go back to commissions list") }}
							</Link>
						</div>
					</div>
					<div class="card p-6 h-auto border-0 card-border">
						<div
							class="card-body max-w-xl w-full mx-auto px-0 card-gutterless h-full">
							<form class="space-y-8" @submit.prevent="save()">
								<div>
									<div
										id="alert-additional-content-5"
										class="p-4 border border-gray-300 rounded-lg bg-gray-50 dark:border-gray-600 dark:bg-gray-800"
										role="alert">
										<h3>
											Provide the payout percent each
											upline gets
										</h3>
										<div class="flex items-center">
											<svg
												class="flex-shrink-0 w-4 h-4 me-2 dark:text-gray-300"
												aria-hidden="true"
												xmlns="http://www.w3.org/2000/svg"
												fill="currentColor"
												viewBox="0 0 20 20">
												<path
													d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
											</svg>

											<h3
												class="text-lg font-medium text-gray-800 dark:text-gray-300">
												Please Note:
											</h3>
										</div>
										<div
											class="mt-2 mb-4 text-sm text-gray-800 dark:text-gray-300">
											<p>
												The total paid out will be sum
												of all the levels commission
											</p>
											<p>
												Level #1 is the individual whom
												made the actual referral being
												paid out.
											</p>
										</div>
									</div>
								</div>
								<FormInput
									v-for="(lvl, i) in form.commissions"
									:key="lvl.level"
									:label="`Level # ${lvl.level} commission`"
									v-model="lvl.percent"
									class="col-span-3"
									:type="number"
									:error="form.errors?.commissions?.[i]">
									<template #trail>Percent</template>
								</FormInput>
								<div class="pt-5">
									<div class="flex justify-end gap-3">
										<PrimaryButton
											secondary
											as="button"
											:href="
												route('admin.commissions.index')
											"
											type="button"
											link>
											{{ $t("Cancel") }}
										</PrimaryButton>
										<PrimaryButton
											type="submit"
											:disabled="form.processing"
											primary>
											<Loading
												class="mr-2 -ml-1 inline-block w-5 h-5"
												v-if="form.processing" />
											<span class="text-sm text-white">
												{{ $t("Save Commission") }}
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
