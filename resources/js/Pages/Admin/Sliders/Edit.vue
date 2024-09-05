<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { ArrowLeft } from "lucide-vue-next";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FileUploader from "@/Components/FileUploader.vue";
	import FileUploaderLocal from "@/Components/FileUploaderLocal.vue";
	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import FormSwitch from "@/Components/Switch.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	const props = defineProps({
		title: { required: false, type: String },
		slider: { type: Object, required: true },
	});
	const form = useForm({
		title: props.slider.title,
		description: props.slider.description,
		url: props.slider.url,
		button: props.slider.button,
		image: props.slider.image,
		active: props.slider.active,
		change_slider_image: false,
		image_uri: null,
		image_path: null,
		image_upload: true,
	});
	const save = () =>
		form.put(window.route("admin.sliders.update", props.slider.id));
</script>
<template>
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Create a new slide</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								link
								secondary
								:href="route('admin.sliders.index')">
								<ArrowLeft
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to sliders list") }}
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 mb-12 rounded card-border">
						<div class="card-body p-12 rounded h-full">
							<form class="space-y-8" @submit.prevent="save()">
								<FormInput
									label="Slide Title text"
									v-model="form.title"
									placeholder="Premeire League"
									type="text"
									:error="form.errors.title"
									class="max-w-lg" />

								<FormInput
									label="Slide description text"
									v-model="form.description"
									placeholder="There's Saturday morning action from Monmore, Nottingham & Harlow to trade on."
									type="text"
									:error="form.errors.description" />

								<FormInput
									label="Slide Image/Button Url link"
									v-model="form.url"
									help="The slide will link to this page when clicked"
									type="text"
									:error="form.errors.url" />

								<FormInput
									label="Button text"
									v-model="form.button"
									type="text"
									placeholder="View Odds"
									:error="form.errors.button" />
								<div class="w-full mt-6">
									<div class="flex items-center space-x-4">
										<FormLabel class="mb-4">
											{{ $t("Slide image") }}
										</FormLabel>
										<FormSwitch
											v-model="form.change_slider_image"
											class="mb-4">
											{{ $t("Change Slide Image") }}
										</FormSwitch>
									</div>
									<CollapseTransition>
										<img
											:src="form.image"
											v-show="!form.change_slider_image"
											class="h-auto border p-2 border-gray-150 dark:border-gray-700 rounded sm:max-w-lg w-full" />
									</CollapseTransition>
									<CollapseTransition>
										<div
											class="mt-4"
											v-show="form.change_slider_image">
											<FileUploader
												class="mb-1 h-32 sm:max-w-xl w-full"
												v-if="$page.props.s3"
												v-model="form.image_uri"
												v-model:file="form.image_path"
												:key="refreshId"
												auto />
											<FileUploaderLocal
												v-else
												class="mb-1 sm:max-w-xl w-full"
												v-model="form.image_uri"
												:key="`u-${refreshId}`"
												v-model:file="
													form.image_path
												" />
											<p
												v-if="form.errors.image"
												class="text-red-500">
												{{ form.errors.image }}
											</p>
											<p v-else class="text-xs">
												{{
													$t(
														"slides without images will be hidden",
													)
												}}
											</p>
										</div>
									</CollapseTransition>
								</div>

								<FormSwitch v-model="form.active">
									{{ $t("Active") }}
								</FormSwitch>
								<div class="pt-5">
									<div class="flex gap-3 justify-end">
										<PrimaryButton
											secondary
											as="button"
											:href="route('admin.sliders.index')"
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
												{{ $t("Save Slider") }}
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
