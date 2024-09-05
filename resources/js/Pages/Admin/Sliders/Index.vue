<script setup>
	import { ref } from "vue";

	import { Head, Link, router, useForm } from "@inertiajs/vue3";
	import { debouncedWatch, useUrlSearchParams } from "@vueuse/core";
	import { HiPencil, HiTrash } from "oh-vue-icons/icons";

	import ConfirmationModal from "@/Components/ConfirmationModal.vue";
	import Loading from "@/Components/Loading.vue";
	import Pagination from "@/Components/Pagination.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import SearchInput from "@/Components/SearchInput.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";

	defineProps({
		sliders: Object,
		title: { required: false, type: String },
	});

	const params = useUrlSearchParams("history");
	const search = ref(params.search ?? "");
	const deleteSliderForm = useForm({});
	const sliderBeingDeleted = ref(null);

	const deleteSlider = () => {
		deleteSliderForm.delete(
			window.route("admin.sliders.destroy", sliderBeingDeleted.value?.id),
			{
				preserveScroll: true,
				preserveState: true,
				onSuccess: () => (sliderBeingDeleted.value = null),
			},
		);
	};
	debouncedWatch(
		[search],
		([search]) => {
			router.get(
				window.route("admin.sliders.index"),
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

	const toggle = (slider) => {
		slider.busy = true;
		router.put(
			window.route("admin.sliders.toggle", slider.id),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					slider.busy = false;
					sliderBeingDeleted.value = null;
				},
			},
		);
	};
</script>
<template>
	<Head :title="title ?? 'Sliders'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ $t("Carousel slides") }}
							</h3>
							<p>
								{{
									$t("Only Max five slides will be displayed")
								}}
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								:href="route('admin.sliders.create')"
								primary
								link>
								Create new slide
							</PrimaryButton>
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
													{{ $t("Title") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Description") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Url") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Button") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Image") }}
												</th>
												<th
													scope="col"
													class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
													{{ $t("Active") }}
												</th>
												<td role="columnheader"></td>
											</tr>
										</thead>
										<tbody role="rowgroup">
											<tr
												v-for="slider in sliders.data"
												:key="slider.id"
												role="row">
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
													{{ slider.title }}
												</td>
												<td
													class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
													{{ slider.description }}
												</td>
												<td
													class="px-6 py-4 text-sm break-words font-medium text-gray-900 dark:text-white">
													{{ slider.url }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
													{{ slider.button }}
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
													<img
														:src="slider.image"
														class="w-12 h-auto" />
												</td>
												<td
													class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
													<Switch
														:modelValue="
															slider.active
														"
														@update:modelValue="
															toggle(slider)
														">
														<template
															v-if="
																slider.active
															">
															{{ "Slide Active" }}
														</template>
														<template v-else>
															{{
																"Slide Disabled"
															}}
														</template>
													</Switch>
												</td>
												<td role="cell">
													<div
														class="flex justify-end text-lg">
														<Link
															:href="
																route(
																	'admin.sliders.edit',
																	{
																		slider: slider.id,
																	},
																)
															"
															class="cursor-pointer p-2 hover:text-blue-600">
															<VueIcon
																:icon="HiPencil"
																class="w-4 h-4" />
														</Link>
														<a
															href="#"
															@click.prevent="
																sliderBeingDeleted =
																	slider
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
								<Pagination :meta="sliders.meta" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
		<ConfirmationModal
			:show="sliderBeingDeleted"
			@close="sliderBeingDeleted = null">
			<template #title>
				{{
					$t("Are you sure about deleting #{slider} ?", {
						slider: sliderBeingDeleted.title,
					})
				}}
			</template>

			<template #content>
				<p>
					{{
						$t(
							"This Action will remove the slider from the database and cannot be undone",
						)
					}}
				</p>
			</template>

			<template #footer>
				<PrimaryButton
					primary
					class="uppercase text-xs font-semibold"
					@click="sliderBeingDeleted = null">
					{{ $t("Cancel") }}
				</PrimaryButton>

				<PrimaryButton
					secondary
					class="ml-2 uppercase text-xs font-semibold"
					v-if="sliderBeingDeleted.active"
					@click="toggle(sliderBeingDeleted)">
					<Loading v-if="sliderBeingDeleted.busy" />
					{{ $t("Disable") }}
				</PrimaryButton>

				<PrimaryButton
					error
					class="ml-2 uppercase text-xs font-semibold"
					@click="deleteSlider"
					:class="{ 'opacity-25': deleteSliderForm.processing }"
					:disabled="deleteSliderForm.processing">
					{{ $t("Delete") }}
				</PrimaryButton>
			</template>
		</ConfirmationModal>
	</AdminLayout>
</template>
