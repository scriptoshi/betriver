<script setup>
import {onMounted, ref} from "vue";

import {useElementHover} from "@vueuse/core";
import {HiSearch, HiX} from "oh-vue-icons/icons";

import VueIcon from "@/Components/VueIcon.vue";
const el = ref();
const hover = useElementHover(el);
const emit = defineEmits(["update:modelValue"]);
defineProps({
	modelValue: String,
	loading: Boolean,
});
const input = ref(null);
onMounted(() => {
	if (input.value.hasAttribute("autofocus")) {
		input.value.focus();
	}
});
defineExpose({focus: () => input.value.focus()});
const reset = () => {
	input.value.value = "";
	emit("update:modelValue", "");
};
</script>
<template>
	<div
		class="w-full"
		ref="el"
	>
		<div class="relative rounded-md shadow-sm">
			<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
				<VueIcon
					:icon="HiSearch"
					class="text-gray-400 w-5 h-5"
				/>
			</div>
			<input
				ref="input"
				type="text"
				name="price"
				class="bg-white px-10 border-gray-300 text-gray-900 rounded-md focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white py-2 text-sm border block w-full focus:outline-none focus:ring-1 appearance-none"
				placeholder="Search"
				aria-describedby="price-currency"
				:value="modelValue"
				@input="$emit('update:modelValue', $event.target.value)"
			/>
			<a
				v-if="hover"
				href="#"
				class="absolute inset-y-0 right-0 pr-3 flex items-center"
				@click="reset()"
			>
				<VueIcon
					:icon="HiX"
					class="text-gray-400 w-5 h-5"
				/>
			</a>
		</div>
	</div>
</template>
