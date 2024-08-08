<script setup>
import { computed, onMounted, ref } from "vue";

import { FaceSmileIcon } from "@heroicons/vue/24/solid";
import { createPopper } from "@popperjs/core";
import { onClickOutside } from "@vueuse/core";
import EmojiPicker from "vue3-emoji-picker";
import "../../../node_modules/vue3-emoji-picker/dist/style.css";
const emit = defineEmits(["update:modelValue"]);
const props = defineProps({
    modelValue: String,
    emoji: { type: Boolean, default: true },
    rows: { type: Number, default: 5 },
    placeholder: { type: String, default: "Enter Details..." },
});
const textarea = ref(null);
const open = ref(false);
const picker = ref(null);
const button = ref(null);
const cursor = ref(null);
onClickOutside(picker, () => (open.value = false));
onMounted(() => {
    if (button.value && picker.value) {
        const offset = 6;
        createPopper(button.value, picker.value, {
            placement: "right-end",
            modifiers: [
                {
                    name: "offset",
                    options: {
                        offset: [0, offset],
                    },
                },
            ],
        });
    }
});

const onSelect = (emoji) => {
    if (cursor.value !== -1) {
        emit(
            "update:modelValue",
            `${props.modelValue.slice(0, cursor.value)}${
                emoji.i
            }${props.modelValue.slice(cursor.value)}`
        );
        cursor.value += emoji.i.length;
    } else {
        emit("update:modelValue", props.modelValue + emoji.i);
    }
    open.value = false;
};

const updateCursor = () => {
    if (textarea.value) {
        cursor.value = textarea.value?.selectionEnd || -1;
    }
};
const modelVal = computed({
    set(val) {
        emit("update:modelValue", val);
    },
    get() {
        return props.modelValue;
    },
});
</script>
<template>
    <div class="flex items-start space-x-4">
        <div class="min-w-0 flex-1 relative">
            <div
                class="border p-4 bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-600 rounded-lg shadow-sm overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500"
            >
                <textarea
                    ref="textarea"
                    :rows="rows"
                    v-model="modelVal"
                    @blur="updateCursor"
                    @keyup="updateCursor"
                    class="block w-full border-0 text-gray-700 dark:text-gray-300 resize-none focus:ring-0 sm:text-sm outline-none focus:outline-none bg-transparent"
                    :placeholder="placeholder"
                />
                <!-- Spacer textareaent to match the height of the toolbar -->
                <div aria-hidden="true">
                    <!-- Matches height of button in toolbar (1px border + 36px content height) -->
                    <div class="py-px">
                        <div class="h-9" />
                    </div>
                </div>
            </div>

            <div
                ref="close"
                class="absolute bottom-0 inset-x-0 pl-3 pr-2 py-2 flex justify-between"
            >
                <div v-if="emoji" class="flex items-center space-x-5">
                    <div class="flex items-center">
                        <button
                            ref="button"
                            type="button"
                            @click="open = !open"
                            class="-m-2.5 w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-500"
                        >
                            <FaceSmileIcon
                                class="flex-shrink-0 h-5 w-5"
                                aria-hidden="true"
                            />
                        </button>
                        <div ref="picker">
                            <EmojiPicker
                                class=""
                                v-if="open"
                                @select="onSelect"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <slot name="clear">
                        <button
                            type="button"
                            @click="$emit('update:modelValue', '')"
                            class="ease-in-out duration-150 inline-flex items-center px-4 py-1 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                        >
                            Clear
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>
