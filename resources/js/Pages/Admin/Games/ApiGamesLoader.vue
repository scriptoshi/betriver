<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { PrCloudDownload } from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	const props = defineProps({
		sport: String,
	});
	const gameForm = useForm({
		sport: props.sport.toLowerCase(),
		start: 0,
		days: 1,
	});
	const agoForm = useForm({
		sport: props.sport.toLowerCase(),
		start: 0,
		days: 1,
	});

	const loadData = () => {
		gameForm.put(window.route("admin.games.load"), {
			preserveScroll: true,
			preserveState: true,
		});
	};
	const loadScores = () => {
		agoForm
			.transform((data) => ({
				...data,
				start: data.days * -1,
				days: data.days,
			}))
			.put(window.route("admin.games.load"), {
				preserveScroll: true,
				preserveState: true,
			});
	};
</script>
<template>
	<div class="text-gray-500 bg-white dark:bg-gray-800 p-3 mb-4">
		<div>
			<div class="mb-3">
				<p>
					Load data for any number of days starting at any day from
					now.
				</p>
			</div>
			<div class="grid lg:grid-cols-2 gap-3">
				<div class="p-3 border dark:border-gray-600">
					<h3 class="text-base mb-2">Load Games from API</h3>
					<div class="flex items-end space-x-3">
						<FormInput
							v-model="gameForm.start"
							:error="gameForm.errors.start"
							label="Start in days from now"
							size="sm max-w-200 w-full">
							<template #trail>
								<span class="text-gray-600 dark:text-gray-400">
									days
								</span>
							</template>
						</FormInput>
						<FormInput
							v-model="gameForm.days"
							:error="gameForm.errors.days"
							label="Load games for days"
							size="sm max-w-200 w-full">
							<template #trail>
								<span class="text-gray-600 dark:text-gray-400">
									days
								</span>
							</template>
						</FormInput>
						<PrimaryButton @click="loadData" secondary>
							<Loading v-if="gameForm.processing" />
							<VueIcon
								v-else
								:icon="PrCloudDownload"
								class="mr-1 -ml-1" />
							Games
						</PrimaryButton>
					</div>
				</div>
				<div class="p-3 border dark:border-gray-600">
					<h3 class="text-base mb-2">Load Scores/results from API</h3>
					<div class="flex items-end space-x-3">
						<FormInput
							v-model="agoForm.days"
							:error="agoForm.errors.days"
							label="Num days ago & today"
							size="sm max-w-sm w-full">
							<template #trail>
								<span class="text-gray-600 dark:text-gray-400">
									days
								</span>
							</template>
						</FormInput>
						<PrimaryButton @click.prevent="loadScores" secondary="">
							<Loading v-if="agoForm.processing" />
							Load Scores for {{ agoForm.days * 1 + 1 }} days
						</PrimaryButton>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
