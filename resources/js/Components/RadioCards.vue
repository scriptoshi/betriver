<script setup>
	import { computed } from "vue";

	import {
		RadioGroup,
		RadioGroupDescription,
		RadioGroupLabel,
		RadioGroupOption,
	} from "@headlessui/vue";

	import Flag from "./Flag";
	const props = defineProps({
		modelValue: [String, Boolean, Number],
		options: Array,
		label: String,
		grid: Number,
	});
	const emit = defineEmits(["update:modelValue"]);
	const value = computed({
		get() {
			return props.modelValue;
		},
		set(value) {
			emit("update:modelValue", value);
		},
	});
	const gridcols = computed(() => {
		if ((props.grid ?? props.options.length) <= 2) return "sm:grid-cols-2";
		if ((props.grid ?? props.options.length) === 3) return "sm:grid-cols-3";
		if ((props.grid ?? props.options.length) === 4) return "sm:grid-cols-4";
		if ((props.grid ?? props.options.length) === 5) return "sm:grid-cols-5";
		if ((props.grid ?? props.options.length) === 6) return "sm:grid-cols-6";
		return "sm:grid-cols-4";
	});
</script>
<template>
	<RadioGroup v-model="value">
		<RadioGroupLabel
			class="text-base font-medium text-gray-900 dark:text-gray-200">
			{{ label }}
		</RadioGroupLabel>
		<div :class="gridcols" class="mt-4 grid grid-cols-1 gap-y-6 sm:gap-x-4">
			<RadioGroupOption
				as="template"
				v-for="option in options"
				:key="option.key"
				:value="option.value"
				v-slot="{ checked, active }">
				<div
					:class="[
						checked
							? 'border-transparent'
							: 'border-gray-300 dark:border-gray-600',
						active ? 'ring-1 ring-emerald-500' : '',
						'relative bg-white dark:bg-gray-850 border rounded shadow-sm px-4 py-2 flex cursor-pointer focus:outline-none',
					]">
					<div class="flex-1 flex">
						<slot v-if="option.img" name="img" :img="option.img">
							<Flag
								v-if="option.img.length == 3"
								:iso="option.img"
								class="w-7 h-7 mr-3 rounded-full" />
							<img
								v-else-if="option.img"
								:src="option.img"
								class="w-7 h-7 mr-3 rounded-full" />
						</slot>

						<div class="flex flex-col">
							<RadioGroupLabel
								as="span"
								class="block text-sm font-semibold text-gray-900 dark:text-gray-300 dark:text-gray-200">
								{{ option.title ?? option.label }}
							</RadioGroupLabel>
							<RadioGroupDescription
								as="span"
								class="flex items-center text-xs text-gray-500 dark:text-gray-400">
								{{ option.subtitle }}
							</RadioGroupDescription>
						</div>
					</div>
					<svg
						id="Layer_1"
						data-name="Layer 1"
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 122.881 122.879"
						:class="[
							!checked ? 'invisible' : '',
							'h-5 w-5 text-emerald-600 dark:text-emerald-400',
						]">
						<path
							d="M61.44,0A61.44,61.44,0,1,1,0,61.439,61.44,61.44,0,0,1,61.44,0Z"
							class="fill-emerald-600 dark:fill-emerald-400" />
						<path
							d="M34.106,68.678l-.015-.014a3.875,3.875,0,0,1-.272-5.446l.029-.031a3.873,3.873,0,0,1,5.46-.258L52.264,74.677,83.353,42.118h0a3.887,3.887,0,0,1,5.49-.137h0a3.886,3.886,0,0,1,.137,5.491l-33.7,35.3a.762.762,0,0,1-.073.067,3.879,3.879,0,0,1-5.342.13L34.118,68.688l-.012-.01Z"
							class="fill-white" />
					</svg>
					<div
						:class="[
							active ? 'border' : 'border',
							checked
								? 'border-emerald-600 dark:border-emerald-400'
								: 'border-transparent',
							'absolute -inset-px rounded pointer-events-none',
						]"
						aria-hidden="true" />
				</div>
			</RadioGroupOption>
		</div>
	</RadioGroup>
</template>
