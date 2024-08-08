<script setup>
import { computed } from "vue";

import { RadioGroup, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";
const props = defineProps({
    options: { type: Array, required: true },
    modelValue: [Number, String, Boolean],
    grid: Number,
});
const classes = computed(() => {
    if ((props.grid ?? props.options.length) <= 2) return "sm:grid-cols-2";
    if ((props.grid ?? props.options.length) === 3) return "sm:grid-cols-3";
    if ((props.grid ?? props.options.length) === 4) return "sm:grid-cols-4";
    if ((props.grid ?? props.options.length) === 5) return "sm:grid-cols-5";
    if ((props.grid ?? props.options.length) === 6) return "sm:grid-cols-6";
    return "sm:grid-cols-4";
});
const emit = defineEmits(["update:modelValue"]);
const updateModelValue = (mv) => emit("update:modelValue", mv);
</script>
<template>
    <RadioGroup
        @update:model-value="updateModelValue"
        :model-value="modelValue"
    >
        <div :class="classes" class="grid grid-cols-2 gap-3">
            <RadioGroupOption
                as="template"
                v-for="item in options"
                :key="item.key ?? item.value"
                :value="item.value"
                v-slot="{ active, checked }"
            >
                <div
                    :class="[
                        'cursor-pointer focus:outline-none',
                        active
                            ? 'ring-2 dark:ring-0 ring-offset-2 dark:ring-offset-0 ring-transparent dark:ring-transparent'
                            : '',
                        checked
                            ? 'border-amber-600 dark:border-amber-400 border-2 text-amber-600  dark:text-amber-400 hover:ring-transparent dark:transparent'
                            : 'bg-white border dark:bg-gray-800 border-gray-200 dark:border-2 dark:border-gray-500 text-gray-900 hover:bg-gray-50 ',
                        'border rounded-[3px] p-2 flex items-center justify-center text-sm font-medium uppercase sm:flex-1 transition-colors duration-500',
                    ]"
                >
                    <RadioGroupLabel
                        class="flex flex-row align-middle items-center text-gray-500 dark:text-gray-100"
                        as="p"
                    >
                        <img
                            v-if="item.img"
                            :src="item.img"
                            class="h-7 w-7 mr-2 cursor-pointer rounded-full border border-slate-300/70 dark:border-slate-600/70 bg-white dark:bg-gray-900 p-0.5"
                        />
                        <span
                            class="transition-colors duration-300"
                            :class="
                                checked
                                    ? 'text-amber-600 dark:text-amber-400'
                                    : 'text-gray-700 dark:text-gray-400'
                            "
                        >
                            {{ item.label }}
                        </span>
                    </RadioGroupLabel>
                </div>
            </RadioGroupOption>
        </div>
    </RadioGroup>
</template>
