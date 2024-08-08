<script setup>
import { computed, ref, watch } from "vue";

import {
    ArrowUpTrayIcon,
    ClipboardDocumentCheckIcon,
    ClipboardDocumentIcon,
    CloudArrowUpIcon,
    XMarkIcon,
} from "@heroicons/vue/24/outline";
import axios from "axios";

import Loading from "@/Components/Loading.vue";
import WeCopy from "@/Components/WeCopy.vue";
const props = defineProps({
    modelValue: String,
    file: String,
    errors: String,
    auto: Boolean,
    deleteUri: String,
    disk: String,
});
const circumference = 30 * 2 * Math.PI;
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
            lgo.type
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
    <div class="flex items-center align-middle">
        <span class="text-red-600 dark:text-red-400" v-if="logoError">{{
            logoError
        }}</span>
        <WeCopy
            :text="preview"
            v-else-if="spacesPath"
            v-slot="{ copied }"
            after
        >
            <ClipboardDocumentCheckIcon v-if="copied" class="w-5 h-6" />
            <ClipboardDocumentIcon v-else class="w-5 h-6" />
        </WeCopy>
        <div
            class="inline-flex items-center justify-center overflow-hidden rounded-full bottom-5 left-5"
        >
            <!-- Building a Progress Ring: https://css-tricks.com/building-progress-ring-quickly/ -->
            <svg class="w-20 h-20">
                <circle
                    class="text-gray-300"
                    stroke-width="5"
                    stroke="currentColor"
                    fill="transparent"
                    r="30"
                    cx="40"
                    cy="40"
                />
                <circle
                    class="text-emerald-600 dark:text-emerald-400"
                    stroke-width="5"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="
                        circumference - (percent / 100) * circumference
                    "
                    stroke-linecap="round"
                    stroke="currentColor"
                    fill="transparent"
                    r="30"
                    cx="40"
                    cy="40"
                />
            </svg>
            <label
                :class="
                    preview
                        ? ''
                        : ' bg-emerald-500 hover:bg-emerald-600  hover:shadow-emerald-500/50 focus:bg-emerald-600 active:bg-emerald-600/90'
                "
                class="absolute btn h-9 w-9 rounded-full p-0 font-medium text-white hover:shadow-lg"
            >
                <input
                    tabindex="-1"
                    type="file"
                    @input="setLogo($event.target.files[0])"
                    class="pointer-events-none absolute inset-0 h-full w-full opacity-0"
                />
                <img
                    v-if="preview"
                    class="w-9 h-9 rounded-full"
                    :src="preview"
                />
                <ArrowUpTrayIcon v-else class="h-5 w-5" />
            </label>
        </div>
        <button
            v-if="spacesPath"
            @click.prevent="clearFile()"
            class="btn space-x-2 bg-error text-base py-1.5 font-medium text-white hover:bg-error hover:shadow-lg hover:shadow-error/50 focus:bg-error focus:shadow-lg focus:shadow-error/50 active:bg-error/90"
        >
            <span>Delete</span>
            <Loading class="w-4 h-4 text-white ml-2 -mr-1" v-if="deleting" />
            <XMarkIcon v-else class="w-4 h-4 text-white ml-2 -mr-1" />
        </button>
        <button
            v-else-if="logo"
            @click.prevent="uploadToSpaces()"
            :disabled="busy"
            class="btn space-x-2 bg-info text-base py-1.5 font-medium text-white hover:bg-info hover:shadow-lg hover:shadow-info/50 focus:bg-info focus:shadow-lg focus:shadow-info/50 active:bg-info/90"
        >
            <span v-if="busy">Uploading</span>
            <span v-else>Upload</span>
            <Loading v-if="busy" />
            <CloudArrowUpIcon v-else class="h-6 w-6" />
        </button>
    </div>
</template>
<style></style>
