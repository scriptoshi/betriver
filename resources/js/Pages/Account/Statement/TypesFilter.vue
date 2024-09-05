<script setup>
	import { computed } from "vue";

	import { usePage } from "@inertiajs/vue3";
	import { ChevronDown } from "lucide-vue-next";

	import { Button } from "@/Components/ui/button";
	import {
		Popover,
		PopoverContent,
		PopoverTrigger,
	} from "@/Components/ui/popover";
	import { cn } from "@/utils";
	const props = defineProps({
		types: String,
	});
	const emit = defineEmits(["update:types"]);
	const selected = computed({
		get: () => [...(props.types?.split(",") ?? [])],
		set: (val) => emit("update:types", val.length ? val.join(",") : null),
	});
	const buttonlabel = computed(() => {
		if (selected.value.length === 1) {
			const items = usePage().props.typeOptions;
			return items[selected.value[0]]?.name;
		}
		if (selected.value.length === 0) return "Any Activity";
		return `${selected.value.length} Activity Types`;
	});
</script>

<template>
	<Popover v-slot="{ open }">
		<PopoverTrigger as-child>
			<Button
				variant="outline"
				:class="
					cn(
						'w-full sm:w-40 justify-between text-left font-normal !border !border-emerald-500',
						selected.length === 0 && 'text-muted-foreground',
					)
				">
				<span>{{ buttonlabel ?? $t("Any type") }}</span>
				<ChevronDown
					:class="{ 'rotate-180': open }"
					class="ml-2 -mr-1 h-4 w-4 transition-transform duration-300" />
			</Button>
		</PopoverTrigger>
		<PopoverContent side="bottom" align="start" class="w-auto p-0">
			<div class="grid w-full sm:w-40 gap-3 p-3">
				<label
					v-for="item in $page.props.typeOptions"
					:key="item.value"
					class="flex items-center flex-row space-x-3 space-y-0">
					<input
						type="checkbox"
						:id="item.value"
						:value="item.value"
						v-model="selected"
						class="form-checkbox basic size-5 rounded-sm border-gray-400/70 checked:bg-gray-500 checked:border-gray-500 hover:border-gray-500 focus:border-gray-500 dark:border-navy-400 dark:checked:bg-navy-400" />
					<span class="font-normal">
						{{ item.name }}
					</span>
				</label>
			</div>
		</PopoverContent>
	</Popover>
</template>
