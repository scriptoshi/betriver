<script setup>
	import { ref } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { uid } from "uid";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import LogoInput from "@/Components/LogoInput.vue";
	import LogoInputLocal from "@/Components/LogoInputLocal.vue";
	import fakeLogo from "@/Components/no-image-available-icon.jpeg?url";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	const props = defineProps({
		photo: String,
	});
	const form = useForm({
		profile_photo_uri: props.photo,
		profile_photo_upload: false,
		profile_photo_path: false,
	});
	const refreshId = ref(uid());
	const updateProfilePhoto = () => {
		form.post(window.route("profile.update.photo"), {
			errorBag: "updateProfilePhoto",
			preserveScroll: true,
			onFinish() {
				form.reset();
				refreshId.value = uid();
			},
		});
	};
</script>
<template>
	<div>
		<div>
			<div class="gap-x-3 sm:col-span-2 grid md:grid-cols-2">
				<FormInput
					v-model="form.profile_photo_uri"
					disabled
					placeholder="https://"
					:error="form.errors.profile_photo_uri"
					:help="$t('Supports png, jpeg or svg')">
					<template #label>
						<div class="flex">
							<span class="mr-3">{{ $t("Profile Photo") }}</span>
							<label class="inline-flex items-center space-x-2">
								<input
									v-model="form.profile_photo_upload"
									class="form-switch h-5 w-10 rounded-full bg-slate-300 before:rounded-full before:bg-slate-50 checked:!bg-emerald-600 checked:before:bg-white dark:bg-navy-900 dark:before:bg-navy-300 dark:checked:before:bg-white"
									type="checkbox" />
								<span>{{ $t("Upload to server") }}</span>
							</label>
						</div>
					</template>
				</FormInput>
				<template v-if="form.profile_photo_upload">
					<LogoInput
						v-if="$page.props.uploadsDisk != 'public'"
						v-model="form.profile_photo_uri"
						v-model:file="form.profile_photo_path"
						:key="refreshId"
						auto />
					<LogoInputLocal
						v-else
						:key="`d-${refreshId}`"
						v-model="form.profile_photo_uri"
						v-model:file="form.profile_photo_path" />
				</template>
				<img
					v-else
					class="w-12 h-12 my-auto rounded-full b-0"
					:src="form.profile_photo_uri ?? fakeLogo" />
			</div>
			<p v-if="form.errors.logo" class="text-red-500">
				{{ form.errors.logo }}
			</p>
			<p v-else class="text-xs">
				{{ $t("JPG/PNG/SVG") }}
			</p>
		</div>
		<div class="mt-6">
			<CollapseTransition>
				<p
					v-show="form.recentlySuccessful"
					class="mb-3 text-green-500 dark:text-green-400">
					Saved successfully
				</p>
			</CollapseTransition>
			<PrimaryButton
				class="uppercase text-xs font-semibold"
				@click.prevent="updateProfilePhoto">
				<Loading v-if="form.processing" class="mr-2 -ml-1" />
				{{ $t("Update Profile Photo") }}
			</PrimaryButton>
		</div>
	</div>
</template>
