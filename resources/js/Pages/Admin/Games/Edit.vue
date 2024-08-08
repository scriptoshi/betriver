<script setup>
	import { useForm } from "@inertiajs/vue3";
	import { UseTimeAgo } from "@vueuse/components";
	import { DateTime } from "luxon";
	import {
		HiArrowLeft,
		HiSolidChevronDown,
		HiSolidX,
	} from "oh-vue-icons/icons";
	import { DatePicker } from "v-calendar";

	import FormLabel from "@/Components/FormLabel.vue";
	import Loading from "@/Components/Loading.vue";
	import MultiSelect from "@/Components/Multiselect/Multiselect.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	const props = defineProps({
		title: String,
		game: Object,
		leagues: Object,
		teams: Object,
		statuses: Object,
	});
	const form = useForm({
		league_id: props.game.league_id,
		home_team_id: props.game.home_team_id,
		away_team_id: props.game.away_team_id,
		startTime: DateTime.fromISO(props.game.startTime).toJSDate(),
		status: props.game.status,
	});
	const save = () =>
		form.put(window.route("admin.games.update", props.game.id));
</script>
<template>
	<Head :title="title ?? `New Game`" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">Update game</h3>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								link
								secondary
								:href="route('admin.games.index', sport)">
								<VueIcon
									:icon="HiArrowLeft"
									class="w-4 h-4 -ml-2 mr-2 inline-block" />
								{{ $t("Go Back to games list") }}
							</PrimaryButton>
						</div>
					</div>
					<div class="card border-0 card-border">
						<div class="card-body p-10 card-gutterless">
							<form class="space-y-8" @submit.prevent="save()">
								<div class="grid sm:grid-cols-2">
									<div>
										<FormLabel class="mb-1">
											{{ $t("Game League") }}
										</FormLabel>
										<MultiSelect
											:options="leagues"
											valueProp="value"
											label="label"
											class="md"
											:placeholder="$t('Select a league')"
											v-model="form.league_id"
											searchable
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<VueIcon
													@click="clear"
													class="mr-1 relative z-10 opacity-60 w-5 h-5"
													:icon="HiSolidX" />
											</template>
										</MultiSelect>
									</div>
								</div>
								<div class="grid gap-4 sm:grid-cols-2">
									<div>
										<FormLabel class="mb-1">
											{{ $t("Home Team") }}
										</FormLabel>
										<MultiSelect
											:options="teams"
											valueProp="value"
											label="label"
											:placeholder="$t('Select a team')"
											v-model="form.home_team_id"
											searchable
											class="md"
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<VueIcon
													@click="clear"
													class="mr-1 relative z-10 opacity-60 w-5 h-5"
													:icon="HiSolidX" />
											</template>
										</MultiSelect>
									</div>
									<div>
										<FormLabel class="mb-1">
											{{ $t("Away Team") }}
										</FormLabel>
										<MultiSelect
											:options="teams"
											valueProp="value"
											label="label"
											:placeholder="$t('Select a team')"
											v-model="form.away_team_id"
											searchable
											class="md"
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
											<template #clear="{ clear }">
												<VueIcon
													@click="clear"
													class="mr-1 relative z-10 opacity-60 w-5 h-5"
													:icon="HiSolidX" />
											</template>
										</MultiSelect>
									</div>
								</div>
								<div class="grid gap-4 sm:grid-cols-2">
									<div>
										<FormLabel class="mb-1">
											{{ $t("StartTime") }}
										</FormLabel>
										<DatePicker
											v-model="form.startTime"
											mode="dateTime"
											is24hr>
											<template
												v-slot="{
													inputValue,
													inputEvents,
												}">
												<input
													class="bg-white border-gray-300 text-gray-900 rounded-sm focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white border block w-full focus:outline-none focus:ring-1 appearance-none py-2 text-sm pl-2 pr-2"
													:value="inputValue"
													v-on="inputEvents" />
											</template>
										</DatePicker>
										<p
											v-if="form.errors.startTime"
											class="text-red-500">
											{{ form.errors.startTime }}
										</p>
										<UseTimeAgo
											v-else
											v-slot="{ timeAgo }"
											:time="form.startTime">
											<p
												class="text-sx font-semibold text-emerald-500">
												{{ timeAgo }}
											</p>
										</UseTimeAgo>
									</div>
									<div>
										<FormLabel class="mb-1">
											{{ $t("Game Status") }}
										</FormLabel>
										<MultiSelect
											:options="statuses"
											valueProp="value"
											label="label"
											:placeholder="$t('Select a status')"
											v-model="form.status"
											searchable
											class="md"
											closeOnSelect>
											<template #caret="{ isOpen }">
												<VueIcon
													:class="{
														'rotate-180': isOpen,
													}"
													class="mr-3 relative z-10 opacity-60 flex-shrink-0 flex-grow-0 transition-transform duration-500 w-6 h-6"
													:icon="
														HiSolidChevronDown
													" />
											</template>
										</MultiSelect>
									</div>
								</div>

								<div class="pt-5">
									<div
										class="flex items-center space-x-4 justify-end">
										<PrimaryButton
											secondary
											as="button"
											:href="route('admin.games.index')"
											type="button"
											link>
											{{ $t("Cancel") }}
										</PrimaryButton>
										<PrimaryButton
											type="submit"
											:disabled="form.processing">
											<Loading
												class="mr-2 -ml-1 inline-block w-5 h-5"
												v-if="form.processing" />
											<span class="text-sm text-white">
												{{ $t("Save Game") }}
											</span>
										</PrimaryButton>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
