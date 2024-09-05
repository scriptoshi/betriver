<script setup>
	import { computed, ref, watch } from "vue";

	import { ArrowUpTrayIcon, XMarkIcon } from "@heroicons/vue/24/outline";
	import axios from "axios";

	import Loading from "@/Components/Loading.vue";
	const props = defineProps({
		modelValue: String,
		file: String,
		errors: String,
		auto: Boolean,
		deleteUri: String,
		disk: String,
	});

	const logo = ref(null);
	const logoUri = ref(null);
	const percent = ref(0);
	const busy = ref(false);
	const deleteUrl = ref(props.deleteUri);
	const onUploadProgress = (bytes) => {
		percent.value = (bytes / logo.value.size) * 100;
	};

	const emit = defineEmits(["update:modelValue", "update:file"]);
	const spacesPath = computed({
		get() {
			return props.modelValue;
		},
		set(value) {
			emit("update:modelValue", value);
		},
	});
	const logoError = computed({
		get() {
			return props.logoError;
		},
		set(value) {
			emit("update:logoError", value);
		},
	});
	const uploadPath = computed({
		get() {
			return props.file;
		},
		set(value) {
			emit("update:file", value);
		},
	});

	const uploadToSpaces = async () => {
		if (!logo.value) return false;

		const fileExtension = logo.value.type.split("/").pop();
		const {
			data: { url, link, file, remove },
		} = await axios.post(window.route("s3.sign", { disk: props.disk }), {
			ext: fileExtension,
			name: logo.value.name,
			type: logo.value.type,
		});

		await axios.put(url, logo.value, {
			headers: {
				"x-amz-acl": "public-read",
				"Content-Type": logo.value.type,
			},
			onUploadProgress,
			crossdomain: true,
		});
		busy.value = false;
		spacesPath.value = link;
		uploadPath.value = file;
		deleteUrl.value = remove;
	};

	const reader = new FileReader();
	reader.onload = function (e) {
		logoUri.value = e.target.result;
	};

	const setLogo = async (lgo) => {
		if (lgo.size > 512000) {
			return (logoError.value = "Max 512Kb");
		}
		if (
			!["image/jpeg", "image/gif", "image/png", "image/svg+xml"].includes(
				lgo.type,
			)
		)
			return (logoError.value = "Unsupported");
		logoError.value = null;
		busy.value = true;
		logo.value = lgo;
		props.auto && uploadToSpaces();
	};

	watch(logo, (logo) => {
		if (logo) reader.readAsDataURL(logo);
		else logoUri.value = null;
	});
	const deleting = ref(false);
	const clearFile = async () => {
		deleting.value = true;
		if (deleteUrl.value) {
			await axios.delete(deleteUrl.value);
		}
		logoUri.value = null;
		logo.value = null;
		spacesPath.value = null;
		percent.value = 0;
		deleting.value = false;
	};

	const preview = computed(() => {
		return spacesPath.value ?? logoUri.value;
	});
</script>
<template>
	<div
		class="w-full group hover:border-sky-500 dark:hover:border-sky-400 cursor-pointer bg-gray-100 dark:bg-gray-800 rounded border-2 border-gray-300 dark:border-gray-650 border-dashed transition-colors duration-200">
		<label
			class="flex-grow h-full w-full p-0 flex items-center justify-center cursor-pointer font-medium text-white">
			<input
				tabindex="-1"
				type="file"
				@input="setLogo($event.target.files[0])"
				class="pointer-events-none absolute inset-0 h-full w-full opacity-0" />
			<Loading v-if="busy" />
			<img v-else-if="preview" class="w-full h-full" :src="preview" />
			<ArrowUpTrayIcon v-else class="h-5 w-5" />
			<slot></slot>
		</label>
		<div v-if="spacesPath" class="-mt-8 flex justify-end">
			<button
				@click.prevent="clearFile()"
				class="btn space-x-2 z-50 bg-red-500 text-base py-1 font-medium text-white hover:bg-error hover:shadow-lg hover:shadow-error/50 focus:bg-error focus:shadow-lg focus:shadow-error/50 active:bg-error/90">
				<span>Delete</span>
				<Loading
					class="w-4 h-4 text-white ml-2 -mr-1"
					v-if="deleting" />
				<XMarkIcon v-else class="w-4 h-4 text-white ml-2 -mr-1" />
			</button>
		</div>
	</div>
</template>
<style></style>
