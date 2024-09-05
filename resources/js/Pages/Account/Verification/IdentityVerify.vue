<script setup>
	import { ref } from "vue";

	import { useForm } from "@inertiajs/vue3";
	import { ScanEye } from "lucide-vue-next";
	import { uid } from "uid";

	import CollapseTransition from "@/Components/CollapseTransition.vue";
	import FileUploader from "@/Components/FileUploader.vue";
	import FileUploaderLocal from "@/Components/FileUploaderLocal.vue";
	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import RadioSelect from "@/Components/RadioSelect.vue";
	import { Badge } from "@/Components/ui/badge";
	defineProps({
		idTypes: Array,
		personal: Object,
	});

	const form = useForm({
		type: "idcard",
		image_uri: null,
		image_path: null,
		image_upload: true,
	});
	const showForm = ref(false);
	const refreshId = ref(uid());
	const approveKyc = () => {
		form.put(window.route("accounts.verify.identity"), {
			preserveScroll: true,
			preserveState: true,
			onFinish() {
				showForm.value = false;
				refreshId.value = uid();
				form.reset();
			},
		});
	};
</script>

<template>
	<div class="bg-gray-50 dark:bg-gray-850 p-4 rounded">
		<div class="flex items-center">
			<ScanEye
				class="w-16 h-16 text-gray-900 dark:text-white mr-4 stroke-[0.5]" />
			<div>
				<h3 class="text-base font-inter text-gray-800 dark:text-white">
					{{ $t("Proof of Identity") }}
				</h3>
				<h3 class="text-xs mb-1">
					{{
						$t(
							"Submit clear and high resolution documents in order to complete KYC faster",
						)
					}}
				</h3>
				<Badge
					v-if="!$page.props.enableKyc"
					variant="outline"
					class="!border-gray-250 dark:!border-gray-550">
					{{ $t("Not required") }}
				</Badge>
				<Badge
					v-else-if="$page.props.auth.user.isKycVerified"
					variant="outline"
					class="!border-green-500 !text-green-500">
					{{ $t("Complete") }}
				</Badge>
				<template v-else-if="personal.proof_of_identity">
					<div class="flex items-center mt-1 space-x-2">
						<Badge
							variant="outline"
							class="!border-gray-350 dark:!border-gray-550">
							{{ $t("Pending") }}
						</Badge>
						<a @click.prevent="showForm = !showForm" href="#">
							<Badge
								variant="outline"
								class="!border-red-600 !text-red-600 dark:!border-red-400 dark:!text-white">
								{{ $t("Resubmit") }}
							</Badge>
						</a>
					</div>
				</template>
				<Badge v-else variant="destructive">
					{{ $t("Unverified") }}
				</Badge>
			</div>
		</div>
		<CollapseTransition>
			<div
				v-show="
					$page.props.enableKyc &&
					!$page.props.auth.user.isKycVerified &&
					(showForm || !personal.proof_of_identity)
				">
				<div class="mt-8">
					<FormLabel class="mb-2">
						{{ $t("Type of Identification") }}
					</FormLabel>
					<RadioSelect
						v-model="form.type"
						:options="Object.values(idTypes)" />
				</div>
				<div class="w-full mt-6">
					<FormLabel class="mb-4">
						{{ $t("Upload Your Document") }}
					</FormLabel>
					<FileUploader
						class="mb-1 h-32 sm:max-w-sm w-full"
						v-if="$page.props.s3"
						v-model="form.image_uri"
						v-model:file="form.image_path"
						:key="refreshId"
						auto />
					<FileUploaderLocal
						v-else
						class="mb-1 sm:max-w-sm w-full"
						v-model="form.image_uri"
						:key="`u-${refreshId}`"
						v-model:file="form.image_path" />
					<p v-if="form.errors.image" class="text-red-500">
						{{ form.errors.image }}
					</p>
					<p v-else class="text-xs">
						{{ $t("This will overwrite any previous uploads") }}
					</p>
				</div>
				<div class="flex w-full mt-5">
					<PrimaryButton
						@click="approveKyc"
						class="!py-1 uppercase"
						primary>
						<Loading v-if="form.processing" class="mr-2 -ml-1" />
						Submit KYC
					</PrimaryButton>
				</div>
			</div>
		</CollapseTransition>
	</div>
</template>
