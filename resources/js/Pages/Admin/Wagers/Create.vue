<script setup>
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import { Link, useForm } from "@inertiajs/vue3";
	defineProps({
		title: { required: false, type: String },
	});
	const form = useForm({
		ticket_id: "",
		bet_id: "",
		game_id: "",
		odd_id: "",
		scoreType: "",
		odds: "",
		winner: "",
	});
	const save = () => form.post(window.route("wagers.create"));
</script>
<template>
	<Head :title="title ?? `New Wager`" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Add New Wager</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<Link
								:href="route('admin.wagers.index')"
								class="py-2.5 px-5 mr-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
								<ArrowLeftIcon
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to wagers list") }}
							</Link>
						</div>
					</div>
					<div class="card h-full border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<form class="space-y-8" @submit.prevent="save()">
								<FormInput
									:label="Ticket_id"
									v-model="form.ticket_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.ticket_id" />

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
									:label="Odd_id"
									v-model="form.odd_id"
									class="col-span-3"
									:type="number"
									:error="form.errors.odd_id" />

								<FormInput
									:label="ScoreType"
									v-model="form.scoreType"
									class="col-span-3"
									type="text"
									:error="form.errors.scoreType" />

								<FormInput
									:label="Odds"
									v-model="form.odds"
									class="col-span-3"
									:type="number"
									:error="form.errors.odds" />

								<div class="pt-5">
									<div class="flex justify-end">
										<PrimaryButton
											secondary
											as="button"
											:href="route('admin.wagers.index')"
											type="button"
											link>
											{{ $t("Cancel") }}
										</PrimaryButton>
										<PrimaryButton
											type="submit"
											:disabled="form.processing">
											<Loading
												class="mr-2 -ml-1 inline-block w-5 h-5"
												v-if="form.processing" />
											<span class="text-sm text-white">
												{{ $t("Save Wager") }}
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
