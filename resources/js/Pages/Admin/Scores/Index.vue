<script setup>
	import { Head } from "@inertiajs/vue3";
	import { HiSolidArrowLeft } from "oh-vue-icons/icons";

	import PrimaryButton from "@/Components/PrimaryButton.vue";
	import VueIcon from "@/Components/VueIcon.vue";
	import AdminLayout from "@/Layouts/AdminLayout.vue";
	import Score from "@/Pages/Admin/Scores/Score.vue";
	defineProps({
		scores: Object,
		game: Object,
		title: String,
	});
</script>
<template>
	<Head :title="title ?? 'Scores'" />
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
							<p>
								{{
									$t(
										"Providing game scores could potentially automatically settle bets on the game",
									)
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
					<Score
						v-for="score in scores"
						:key="score.type"
						:score="score"
						:game="game" />
				</div>
			</div>
		</main>
	</AdminLayout>
</template>
