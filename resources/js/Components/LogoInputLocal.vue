<script setup>
import { computed, reactive, ref } from "vue";

import { usePage } from "@inertiajs/vue3";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginImagePreview from "filepond-plugin-image-preview";
import "filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css";
import vueFilePond from "vue-filepond";

defineProps({
    modelValue: String,
    file: Object,
    errors: String,
    label: String,
});
const emit = defineEmits(["update:modelValue", "update:file"]);
const FilePond = vueFilePond(
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview
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
</script>
<template>
    <div class="flex items-center">
        <div>
            <FilePond
                name="filepond"
                ref="pond"
                class-name="logo filepond fp-bordered label-icon w-20 "
                label-idle="Upload Logo"
                :allow-multiple="false"
                :allowImagePreview="true"
                stylePanelAspectRatio="1:1"
                stylePanelLayout="compact circle"
                labelIdle="<svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewbox='0 0 24 24' stroke='currentColor'>
                                      <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'></path>
                                    </svg>"
                accepted-file-types="image/jpeg, image/png, image/svg+xml"
                :server="server"
                @processfile="handleProcessFile"
            />
        </div>
        <p
            v-if="errors || uploadError"
            class="text-sm mb-4 ml-4 font-medium text-red"
        >
            {{ errors ?? uploadError }}
        </p>
        <p
            v-else-if="label"
            class="text-sm mb-4 ml-4 font-medium text-gray-900 dark:text-gray-300"
        >
            {{ label }}
        </p>
    </div>
</template>
<style>
.filepond.filepond--root[data-style-panel-layout~="circle"]
    .filepond--file
    [data-align*="right"] {
    right: calc(50% - 1em);
    top: calc(50% - 1em);
}
</style>
