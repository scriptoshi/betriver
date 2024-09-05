<script setup>
	import { ref } from "vue";

	import { Head, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiTrash } from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		feedback: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteFeedbackForm = useForm({});
	const feedbackBeingDeleted = ref(null);

	const deleteFeedback = () => {
		deleteFeedbackForm.delete(
			window.route(
				"admin.feedback.destroy",
				feedbackBeingDeleted.value?.id,
			),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (feedbackBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.feedback.index"),
				{ search },
				{
					preserveState: true,
					preserveScroll: true,
				},
			);
		},
		{
			maxWait: 700,
		},
	);
</script>
<template>
	<Head :title="title ?? 'Feedbacks'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("User submitted feedbacks") }}
							</h3>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body px-0 card-gutterless h-full">
							<div
								class="lg:flex items-center justify-between mb-4 px-6">
								<div class="flex gap-x-3 sm:w-1/2 lg:w-1/4">
									<SearchInput
										class="max-w-md"
										v-model="search" />
								</div>
							</div>
							<div>
								<div class="overflow-x-auto">
									<table
										class="table-default table-hover"
										role="table">
										<thead>
											<tr role="row">
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("User") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Feedback") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="feedback in feedback.data"
												:key="feedback.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
													{{ feedback.user.name }}
												</td>
												<td
													class="px-6 py-4 text-sm font-medium text-gray-900">
													<p class="w-full">
														{{ feedback.feedback }}
													</p>
												</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<a
															href="#"
															@click.prevent="
																feedbackBeingDeleted =
																	feedback
															"
															class="cursor-pointer p-2 hover:text-red-500">
															<VueIcon
																:icon="HiTrash"
																class="w-4 h-4" />
														</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<Pagination :meta="feedback.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="feedbackBeingDeleted"
			@close="feedbackBeingDeleted = null">
			<template #title>
				{{ $t("Are you sure about deleting this feedback ?") }}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the feedback from the database and cannot be undone",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="feedbackBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteFeedback"
					:class="{ 'opacity-25': deleteFeedbackForm.processing }"
					:disabled="deleteFeedbackForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
