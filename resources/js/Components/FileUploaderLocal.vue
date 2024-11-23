<script setup>
	import { computed, reactive, ref } from "vue";

	import { usePage } from "@inertiajs/vue3";
	import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
	import FilePondPluginImagePreview from "filepond-plugin-image-preview";
	import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
	import vueFilePond from "vue-filepond";

	defineProps({
		modelValue: String,
	});

	const emit = defineEmits(["update:modelValue"]);
	const FilePond = vueFilePond(
		FilePondPluginFileValidateType,
		FilePondPluginImagePreview,
	);
	const server = reactive({
		url: "/filepond/api",
		process: "/process",
		revert: "/process",
		patch: "?patch=",
		headers: {
			"X-CSRF-TOKEN": computed(() => usePage().props.csrf_token),
		},
	});
	const uploadError = ref(null);
	const handleProcessFile = (error, file) => {
		if (error) uploadError.value = error;
		console.log(file);
		emit("update:file", {
			serverId: file.serverId,
			fileExtension: file.fileExtension,
			fileType: file.fileType,
			filenameWithoutExtension: file.filenameWithoutExtension,
			id: file.id,
		});
		emit("update:modelValue", file.filename);
	};

	const acceptecFiles = [
		"image/jpeg",
		"image/jpg",
		"image/png",
		"image/gif",
		"image/bmp",
		"image/svg+xml",
		"video/mp4",
		"video/webm",
		"video/ogg",
	];
</script>

<template>
	<div
		class="w-full group hover:border-sky-500 dark:hover:border-sky-400 cursor-pointer bg-gray-100 dark:bg-gray-800 rounded border-2 border-gray-300 dark:border-gray-650 border-dashed transition-colors duration-200">
		<label class="cursor-pointer">
			<FilePond
				name="filepond"
				ref="pond"
				class-name="filepond nft-upload !mb-0"
				label-idle="Upload NFT"
				:allow-multiple="false"
				:allowImagePreview="true"
				:allowVideoPreview="true"
				stylePanelLayout="compact"
				labelIdle="<svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewbox='0 0 24 24' stroke='currentColor'>
                                      <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'></path>
                                    </svg>"
				:accepted-file-types="acceptecFiles.join(',')"
				:server="server"
				@processfile="handleProcessFile" />
		</label>
	</div>
</template>
