<script setup>
	import { computed } from "vue";

	import {
		ChevronLeftIcon,
		ChevronRightIcon,
	} from "@heroicons/vue/24/outline";
	import { Link } from "@inertiajs/vue3";
	import { HiSolidChevronDown, HiSolidX } from "oh-vue-icons/icons";

	import Multiselect from "@/Components/Multiselect/Multiselect.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		meta: Object,
		perPage: Number,
		showPerPage: Boolean,
	});
	const emit = defineEmits(["update:perPage"]);
	const perPage = computed({
		get: () => props.perPage,
		set: (val) => emit("update:perPage", val),
	});
</script>
<template>
	<!-- pagination buttons -->
	<div>
		<div
			v-if="meta?.links?.length > 3"
			class="mt-5 flex space-x-2 items-center justify-center">
			<template v-for="link in meta.links" :key="link.label">
				<Link
					v-if="link.label == 'pagination.previous'"
					:href="link.url ?? '#'"
					as="button"
					:disabled="!link.url"
					class="disabled:pointer-events-none disabled:bg-gray-50 dark:disabled:bg-gray-700 disabled:text-gray-400 rounded-lg px-2 py-2 text-gray-500 dark:text-white font-semibold bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-500 transition duration-200">
					<ChevronLeftIcon class="w-6 h-6" />
				</Link>
				<Link
					v-else-if="link.label == 'pagination.next'"
					:href="link.url ?? '#'"
					:disabled="!link.url"
					class="disabled:pointer-events-none disabled:bg-gray-50 dark:disabled:bg-gray-700 disabled:text-gray-400 rounded-lg px-2 py-2 text-gray-500 dark:text-white font-semibold bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-600 hover:border-emerald-500 dark:hover:border-emerald-500 transition duration-200">
					<ChevronRightIcon class="w-6 h-6" />
				</Link>
				<Link
					v-else
					:disabled="link.active"
					:href="link.url ?? '#'"
					:class="
						link.active
							? 'text-emerald-500 dark:text-emerald-400  border-emerald-500 dark:border-emerald-500'
							: ' text-gray-900 dark:text-white  border-gray-200 dark:border-gray-600'
					"
					class="rounded-lg px-4 py-2 font-semibold bg-white dark:bg-gray-800 border-2 hover:border-emerald-500 dark:hover:border-emerald-500 transition duration-200">
					{{ link.label }}
				</Link>
			</template>
			<!-- active class text-emerald-500 border-emerald-500 dark:border-emerald-500 -->
		</div>
		<div class="w-full flex mt-4 items-center justify-center space-x-3">
			<p class="text-center">
				Page {{ meta.current_page }} of {{ meta.last_page }}. Total
				{{ meta.total }}
				Items
			</p>
			<div v-if="showPerPage" class="max-w-[160px] w-full">
				<Multiselect
					:options="[
						{ label: '5 items', value: 5 },
						{ label: '10 items', value: 10 },
						{ label: '25 items', value: 25 },
						{ label: '50 items', value: 50 },
						{ label: '100 items', value: 100 },
						{ label: '500 items', value: 500 },
					]"
					valueProp="value"
					label="label"
					class="sm"
					:placeholder="$t('Show items')"
					v-model="perPage"
					:searchable="false"
					closeOnSelect>
					<template #caret="{ isOpen }">
						<VueIcon
							:class="{
								'rotate-180': isOpen,
							}"
							class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
							:icon="HiSolidChevronDown" />
					</template>
					<template #clear="{ clear }">
						<VueIcon
							@click="clear"
							class="mr-1 relative z-10 opacity-60 w-5 h-5"
							:icon="HiSolidX" />
					</template>
				</Multiselect>
			</div>
		</div>
	</div>
	<!-- ./ pagination buttons -->
</template>
