<script setup>
	import { Head, router, useForm } from "@inertiajs/vue3";
	import { HiSolidArrowLeft } from "oh-vue-icons/icons";

	import FormInput from "@/Components/FormInput.vue";
	import Loading from "@/Components/Loading.vue";
	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import Switch from "@/Components/Switch.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import Accordion from "@/Pages/Admin/Odds/Accordion.vue";
	const props = defineProps({
		title: String,
		odds: Object,
		game: Object,
		markets: Object,
	});
	const form = useForm({
		...props.odds,
	});
	const save = () => form.post(window.route("admin.odds.store"));
	const toggle = (market) => {
		console.log(market);
		market.busy = true;
		router.put(
			window.route("admin.odds.toggle", { gameMarket: market.gm }),
			{},
			{
				preserveScroll: true,
				preserveState: true,
				onFinish: () => {
					market.busy = false;
				},
			},
		);
	};
</script>
<template>
	<Head :title="title ?? 'Odds'" />
	<AdminLayout>
		<main class="h-full">
			<div
				class="relative h-full flex flex-auto flex-col px-4 sm:px-6 py-12 sm:py-6 md:px-8">
				<div class="flex flex-col gap-4 h-full">
					<div
						class="lg:flex items-center justify-between mb-4 gap-3">
						<div class="mb-4 lg:mb-0">
							<h3 class="h3">
								{{ game.name }}
							</h3>
							<p>{{ $t("Prelive Odds") }}</p>
							<p>
								{{
									$t("Please NOTE: ZERO ODDS will be ignored")
								}}
							</p>
						</div>
						<div
							class="flex flex-col lg:flex-row lg:items-center gap-3">
							<PrimaryButton
								link
								secondary
								:href="route('admin.games.index', game.sport)">
								<VueIcon
									class="mr-2 -ml-1 w-4 h-4"
									:icon="HiSolidArrowLeft" />
								{{ $t("Back to games") }}
							</PrimaryButton>
						</div>
					</div>
					<form class="space-y-4" @submit.prevent="save()">
						<Accordion
							v-for="market in markets"
							:key="market.id"
							:initiallyOpen="false">
							<div
								class="w-full flex justify-between pr-4 items-center">
								<h2 class="text-xl flex items-center font-bold">
									<Loading class="mr-2" v-if="market.busy" />
									{{ market.name }}
								</h2>
								<Switch
									@update:modelValue="toggle(market)"
									:modelValue="market.active">
									<template v-if="market.active">
										Market is Active
									</template>
									<template v-else>Disabled</template>
								</Switch>
							</div>
							<template #content>
								<div class="grid sm:grid-cols-3 gap-5">
									<FormInput
										v-for="odd in market.odds"
										:key="odd.md5"
										:label="odd.label"
										v-model="form[odd.md5].odd"
										sm />
								</div>
							</template>
						</Accordion>

						<div class="pt-5">
							<div class="flex gap-4 items-center justify-end">
								<PrimaryButton
									secondary
									as="button"
									:href="route('admin.odds.index', game.uuid)"
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
										{{ $t("Save Odd") }}
									</span>
								</PrimaryButton>
							</div>
						</div>
					</form>
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
