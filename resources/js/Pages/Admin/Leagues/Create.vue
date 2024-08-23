<script setup>
	import { useForm } from "@inertiajs/vue3";
	import {
		HiArrowLeft,
		HiSolidChevronDown,
		HiSolidX,
	} from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import LogoInput from "@/Components/LogoInput.vue";
	import LogoInputLocal from "@/Components/LogoInputLocal.vue";
	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import { countries } from "@/constants/countries";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	const props = defineProps({
		title: String,
		sport: String,
		leagueSports: Array,
		racetags: Array,
	});
	const form = useForm({
		leagueId: "",
		name: "",
		season: "",
		country: "",
		description: "",
		sport: props.sport,
		image_uri: null,
		race_tag: null,
		image_path: null,
		image_upload: false,
	});
	const save = () => form.post(window.route("admin.leagues.store"));
</script>
<template>
	<Head :title="title ?? `New League`" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Add New League</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								link
								secondary
								:href="route('admin.leagues.index', sport)">
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to leagues list") }}
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body p-8 card-gutterless h-full">
							<form class="space-y-8" @submit.prevent="save()">
								<div class="grid sm:grid-cols-3 gap-4">
									<FormInput
										label="Name"
										v-model="form.name"
										type="text"
										placeholder="English Premiere League"
										:error="form.errors.name" />
									<div>
										<FormLabel class="mb-2">
											Category
										</FormLabel>
										<Multiselect
											:options="leagueSports"
											valueProp="value"
											label="label"
											:placeholder="$t('Select a sport')"
											v-model="form.sport"
											searchable
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<a
													href="#"
													class="p-0.5 relative z-10 opacity-60 flex-shrink-0 flex-grow-0"
													@click.prevent="clear">
													<VueIcon
														@click="clear"
														class="mr-1 w-4 h-4"
														:icon="HiSolidX" />
												</a>
											</template>
										</Multiselect>
									</div>
									<div v-if="form.sport === 'racing'">
										<FormLabel class="mb-2">
											Filter Tag
										</FormLabel>
										<Multiselect
											:options="racetags"
											valueProp="value"
											label="label"
											:placeholder="
												$t('Select an option')
											"
											v-model="form.race_tag"
											searchable
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<a
													href="#"
													class="p-0.5 relative z-10 opacity-60 flex-shrink-0 flex-grow-0"
													@click.prevent="clear">
													<VueIcon
														@click="clear"
														class="mr-1 w-4 h-4"
														:icon="HiSolidX" />
												</a>
											</template>
										</Multiselect>
									</div>
								</div>

								<FormInput
									label="Description"
									placeholder="Club Championships in the united Kingdom"
									v-model="form.description"
									type="text"
									:error="form.errors.description" />

								<div>
									<div
										class="gap-x-3 sm:col-span-2 grid md:grid-cols-2">
										<FormInput
											v-model="form.image_uri"
											:disabled="form.image_upload"
											placeholder="https://"
											:error="form.errors.image_uri"
											:help="
												$t('Supports png, jpeg or svg')
											">
											<template #label>
												<div class="flex">
													<span class="mr-3">
														{{ $t("Image") }}
													</span>
													<label
														class="inline-flex items-center space-x-2">
														<input
															v-model="
																form.image_upload
															"
															class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
															type="checkbox" />
														<span>
															{{
																$t(
																	"Upload to server",
																)
															}}
														</span>
													</label>
												</div>
											</template>
										</FormInput>
										<template v-if="form.image_upload">
											<LogoInput
												v-if="$page.props.s3"
												v-model="form.image_uri"
												v-model:file="form.image_path"
												auto />
											<LogoInputLocal
												v-else
												v-model="form.image_uri"
												v-model:file="
													form.image_path
												" />
										</template>
										<img
											v-else
											class="w-12 h-12 my-auto rounded-full b-0"
											:src="form.image_uri ?? fakeLogo" />
									</div>
									<p
										v-if="form.errors.image"
										class="text-red-500">
										{{ form.errors.image }}
									</p>
									<p v-else class="text-xs">
										{{ $t("") }}
									</p>
								</div>
								<div class="grid sm:grid-cols-3 gap-4">
									<div>
										<FormLabel class="mb-2">
											Country
										</FormLabel>
										<Multiselect
											:options="countries"
											valueProp="value"
											label="label"
											:placeholder="$t('Country')"
											v-model="form.country"
											searchable
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<a
													href="#"
													class="p-0.5 relative z-10 opacity-60 flex-shrink-0 flex-grow-0"
													@click.prevent="clear">
													<VueIcon
														@click="clear"
														class="mr-1 w-4 h-4"
														:icon="HiSolidX" />
												</a>
											</template>
										</Multiselect>
										<p
											class="text-red-500"
											v-if="form.errors.country">
											{{ form.errors.country }}
										</p>
									</div>

									<FormInput
										label="Season"
										v-model="form.season"
										type="text"
										placeholder="2024"
										:error="form.errors.season" />
								</div>
								<div class="pt-5">
									<div
										class="flex items-center space-x-3 justify-end">
										<PrimaryButton
											secondary
											as="button"
											:href="route('admin.leagues.index')"
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
												{{ $t("Save League") }}
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
