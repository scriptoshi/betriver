<script setup>
import { computed, defineEmits } from "vue";
const props = defineProps({
    modelValue: { type: [Array, Boolean] },
    disabled: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);
const model = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit("update:modelValue", value);
    },
});
</script>
<template>
    <label :disabled="disabled" class="inline-flex items-center space-x-2">
        <input
            class="crud-switch h-5 w-10 rounded-full bg-gray-300 before:rounded-full before:bg-gray-50 checked:!bg-emerald-500 checked:before:bg-white dark:bg-gray-900 dark:before:bg-gray-300 dark:checked:before:bg-white"
            type="checkbox"
            v-model="model"
            :disabled="disabled"
        />
        <span
            class="text-sm font-medium cursor-pointer text-gray-900 dark:text-gray-300"
        >
            <slot />
        </span>
    </label>
</template>
<style>
.crud-switch,
.crud-switch::before {
    @apply transition-all duration-200 ease-in-out;
}

.crud-switch {
    @apply relative shrink-0 cursor-pointer select-none appearance-none overflow-hidden outline-none
   focus:outline-none;
    print-color-adjust: exact;
}

.crud-switch {
    @apply [--thumb-border:2px] before:absolute focus-visible:ring
  before:top-[var(--thumb-border)] before:left-[var(--thumb-border)]
  before:h-[calc(100%-var(--thumb-border)*2)] 
  before:w-[calc((100%/2)-(var(--thumb-border)*2))]
  checked:before:translate-x-[calc(100%+(var(--thumb-border)*2))];
}

.crud-switch.is-outline {
    @apply before:w-[calc((100%/2)-(var(--thumb-border)*2)-1px)]
  checked:before:translate-x-[calc(100%+(var(--thumb-border)*2)+2px)];
}
</style>
