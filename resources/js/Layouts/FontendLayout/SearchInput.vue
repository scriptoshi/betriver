<script setup>
	import { ref } from "vue";

	import { debouncedWatch } from "@vueuse/core";
	import axios from "axios";
	import { HiChevronRight, HiSearch, HiSolidX } from "oh-vue-icons/icons";

	import EventCard from "@/Components/EventCard.vue";
	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const search = ref("");
	const processing = ref(false);
	const results = ref([]);
	const loadResults = async () => {
		processing.value = true;
		const { data } = await axios.post(window.route("sports.filter"), {
			search: search.value,
		});
		results.value = data;
		processing.value = false;
	};
	debouncedWatch(search, (search) => {
		if (!!search && search.length > 2) loadResults();
	});
</script>
<template>
	<div class="hidden lg:ml-[102px] lg:flex">
		<FormInput
			input-classes="!border-none !bg-gray-200 dark:!bg-gray-800 !py-1.5"
			v-model="search"
			class="w-56"
			size="xs">
			<template #lead>
				<VueIcon :icon="HiSearch" class="text-gray-400 w-4 h-4" />
			</template>
			<template #trail>
				<a
					v-show="search.length > 2"
					@click.stop="search = ''"
					class="pointer-cursor flex items-center hover:text-gray-600 dark:hover:text-gray-300 text-gray-400"
					href="#">
					<VueIcon :icon="HiSolidX" class="w-4 h-4" />
				</a>
			</template>
		</FormInput>
		<div
			v-show="search?.length ?? 0 > 0"
			class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-2 fixed top-[52px] with-shadow z-50 w-[550px] min-h-28 max-h-[cal(100%_-_52px)] overflow-y-auto">
			<template v-if="search.length < 3">
				<span
					class="text-gray-800 px-5 dark:text-white border-b border-gray-200 dark:border-gray-650 flex justify-start py-4 break-words">
					{{
						$t("The search term must be at least 3 characters long")
					}}
				</span>
			</template>
			<template v-else-if="processing">
				<span
					class="text-gray-800 px-5 dark:text-white border-b border-gray-200 dark:border-gray-650 flex justify-start py-4 break-words">
					{{
						$t("Searching for {search} ...", {
							search: search,
						})
					}}
				</span>
				<div class="px-5 py-4 flex items-center justify-center">
					<Loading class="!w-10 !h-10" />
				</div>
			</template>

			<template v-else-if="results.length > 0">
				<span
					class="text-gray-800 px-5 dark:text-white border-b border-gray-200 dark:border-gray-650 flex justify-start py-4 break-words">
					{{
						$t('Top 5 results for "{search}" ...', {
							search: search,
						})
					}}
				</span>
				<div
					v-for="game in results"
					:key="game.id"
					class="relative border-b border-gray-150 dark:border-gray-700">
					<EventCard class="!py-3" :game="game" />
					<VueIcon
						class="w-4 h-4 z-10 pointer-events-none absolute right-2 top-1/2 -translate-y-1/2"
						:icon="HiChevronRight" />
				</div>
			</template>
			<div v-else class="px-5 py-4 flex items-center">
				<span
					class="text-gray-800 px-5 dark:text-white border-b border-gray-200 dark:border-gray-650 flex justify-start py-4 break-words">
					{{
						$t(
							'No results for "{search}". Try changing your search parameters',
							{
								search: search,
							},
						)
					}}
				</span>
			</div>
			<div class="flex px-3 pt-4 items-start justify-start">
				<PrimaryButton
					:disabled="results.length == 0 || search?.length < 3"
					class="!py-1.5 !text-xs">
					SEE ALL RESULTS
					<VueIcon
						:icon="HiChevronRight"
						class="!w-4 !h-4 ml-2 -mr-1" />
				</PrimaryButton>
			</div>
		</div>
	</div>
</template>
<style>
	.with-shadow {
		box-shadow: 0 2px 24px 0 rgba(0, 0, 0, 0.5);
	}
</style>
